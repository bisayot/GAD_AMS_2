<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class PlanController extends ResourceController
{
    protected $modelName = 'App\Models\GpbModel';
    protected $format    = 'json';

    public function getPlan()
    {
        $db = \Config\Database::connect();

        // Get organization settings
        $orgData = [
            'name' => 'Benguet State University',
            'category' => 'State Universities and Colleges',
            'hierarchy' => 'Benguet State University',
            'year' => date('Y'),
            'totalOrgBudget' => 1062488000.00,
            'otherSources' => 0.00,
            'preparedByName' => 'Jude Laoagan Tayaben, GAD Director',
            'approvedByName' => 'Kenneth Alip Laruan, President',
        ];

        if ($db->tableExists('settings')) {
            $settingModel = new \App\Models\SettingModel();
            // Get the fiscal year of the items currently in the database
            $latestItem = $db->table('gpb_items')->select('fiscal_year')->orderBy('id', 'DESC')->get()->getRowArray();
            $latestYear = $latestItem ? $latestItem['fiscal_year'] : date('Y');
            $settings = $settingModel->getByFiscalYear($latestYear);
            if (!empty($settings)) {
                // Only merge keys that are expected in orgData or just merge all
                foreach ($orgData as $k => $v) {
                    if (isset($settings[$k])) {
                        $orgData[$k] = $settings[$k];
                    }
                }
            }
        }

        // Ensure year is strictly numeric so frontend number input displays it
        $orgData['year'] = preg_replace('/[^0-9]/', '', (string)$orgData['year']);
        if (empty($orgData['year'])) {
            $orgData['year'] = date('Y');
        }

        // Get items
        $items = $db->table('gpb_items')
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $processedItems = [];
        foreach ($items as $item) {
            $budgetLines = isset($item['budget_lines']) ? json_decode($item['budget_lines'], true) : [];
            if (!is_array($budgetLines) || empty($budgetLines)) {
                $budgetLines = [];
            }

            $processedItems[] = [
                'id' => (string) $item['id'],
                'section' => $item['section'],
                'mandate' => $item['mandate'],
                'cause' => $item['cause'],
                'result' => $item['result'] ?? $item['objective'],
                'mfo' => $item['mfo'] ?? $item['ppa'],
                'activity' => $item['activity'],
                'indicators' => $item['indicators'] ?? $item['targets'],
                'responsible' => $item['responsible'] ?? $item['office'],
                'budgetLines' => $budgetLines,
            ];
        }

        return $this->respond([
            'settings' => ['apiBaseUrl' => ''],
            'org' => $orgData,
            'items' => $processedItems,
        ]);
    }

    public function savePlan()
    {
        $db = \Config\Database::connect();
        $input = $this->request->getJSON(true);

        // Validate input
        if (!isset($input['org']) || !isset($input['items'])) {
            return $this->failValidationErrors('Org and items data are required');
        }

        // Save organization settings
        $orgData = $input['org'];
        $dynamicYear = !empty($orgData['year']) ? (int)$orgData['year'] : 2026;
        if ($db->tableExists('settings')) {
            $settingModel = new \App\Models\SettingModel();
            foreach ($orgData as $key => $val) {
                $settingModel->saveSetting($key, $val, $dynamicYear);
            }
        }

        // Save items
        $db->table('gpb_items')->emptyTable(); // Safe delete without dropping InnoDB tablespace
        $columns = $db->getFieldNames('gpb_items');
        $sortOrder = 1;
        $batchData = [];
        foreach ($input['items'] as $item) {
            $totalBudget = 0;
            foreach ($item['budgetLines'] as $line) {
                $totalBudget += (float) ($line['amount'] ?? 0);
            }

            $possibleData = [
                'fiscal_year' => $dynamicYear,
                'section' => $item['section'],
                'sort_order' => $sortOrder++,
                'mandate' => $item['mandate'],
                'cause' => $item['cause'],
                'objective' => $item['result'],
                'result' => $item['result'],
                'ppa' => $item['mfo'],
                'mfo' => $item['mfo'],
                'activity' => $item['activity'],
                'targets' => $item['indicators'],
                'indicators' => $item['indicators'],
                'budget' => $totalBudget,
                'source' => isset($item['budgetLines'][0]['source']) ? $item['budgetLines'][0]['source'] : null,
                'office' => $item['responsible'],
                'responsible' => $item['responsible'],
                'budget_lines' => json_encode($item['budgetLines']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $data = [];
            foreach ($possibleData as $field => $value) {
                if (in_array($field, $columns)) {
                    $data[$field] = $value;
                }
            }

            // Preserve original ID if provided as integer, else set to null for auto-increment
            if (isset($item['id']) && ctype_digit((string)$item['id']) && in_array('id', $columns)) {
                $data['id'] = (int) $item['id'];
            } elseif (in_array('id', $columns)) {
                $data['id'] = null; // Forces auto-increment and ensures keys are uniform for insertBatch
            }

            // Ensure consistent key order for CodeIgniter's insertBatch
            ksort($data);

            $batchData[] = $data;
        }

        if (!empty($batchData)) {
            $db->table('gpb_items')->insertBatch($batchData);
        }

        return $this->respond(['status' => 'success', 'message' => 'Plan saved successfully']);
    }
}
