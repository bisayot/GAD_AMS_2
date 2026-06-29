import os, re

views_dir = r'c:\xampp\htdocs\GAD_AMS_2\frontend\src\views'
dirs = ['twg', 'staff', 'admin']
files = ['ADView.vue', 'ARView.vue', 'ADRevision.vue', 'ADReview.vue']

grid_regex = re.compile(
    r'(<div class=\"info-item\">\s*<span class=\"info-label\">Category<\/span>\s*<span class=\"info-value-white\">Activity Design<\/span>\s*<\/div>\s*)<div class=\"info-item\">\s*<span class=\"info-label\">Form Type<\/span>\s*<span class=\"info-value-white uppercase\">\{\{\s*formatFormType\(design\.form_type\)\s*\}\}<\/span>\s*<\/div>',
    re.DOTALL
)

new_grid_content = r'''\1<div class="info-item" style="grid-column: span 2;">
                <span class="info-label">Activity Classification</span>
                <span class="info-value-white">{{ design.activity_classification || '---' }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Form Type</span>
                <span class="info-value-white uppercase">{{ design.form_type_name || formatFormType(design.form_type) || '---' }}</span>
              </div>
              <div class="info-item" style="grid-column: span 2;">
                <span class="info-label">GAD Mandate</span>
                <span class="info-value-white">{{ design.gad_mandate || '---' }}</span>
              </div>
              <div class="info-item" style="grid-column: span 2;">
                <span class="info-label">Gender Issues</span>
                <span class="info-value-white">{{ design.gender_issue || '---' }}</span>
              </div>'''

budget_regex = re.compile(
    r'const items = \[\s*\{\s*name:\s*\'Meals and Snacks \(AM/PM\)\',[^\]]*\];',
    re.DOTALL
)

new_budget_logic = r'''const catering = Number(d.meals_and_snacks || 0) + Number(d.accommodation || 0);
  const venue = Number(d.function_room_venue || 0) + Number(d.equipment_rental || 0) + Number(d.transportation || 0);
  const program = Number(d.professional_fee_honoria || 0) + Number(d.tokens || 0);
  const materials = Number(d.materials_and_supplies || 0);

  const items = [
    { name: 'Catering & Hospitality', total: catering },
    { name: 'Venue & Logistics', total: venue },
    { name: 'Program & Speakers', total: program },
    { name: 'Materials & Miscellaneous', total: materials }
  ];'''

ar_grid_regex = re.compile(
    r'(<label class=\"info-label\">Title<\/label>\s*<p class=\"text-sm-light mt-1\">\{\{\s*report\.activity_design\.activity_title\s*\}\}<\/p>\s*<\/div>\s*)<div>\s*<label class=\"info-label\">Form Type<\/label>\s*<p class=\"text-sm-light mt-1\">\{\{\s*report\.activity_design\.form_type\s*\}\}<\/p>\s*<\/div>',
    re.DOTALL
)

new_ar_grid_content = r'''\1<div class="full-width-info">
                  <label class="info-label">Activity Classification</label>
                  <p class="text-sm-light mt-1">{{ report.activity_design.activity_classification || '---' }}</p>
                </div>
                <div>
                  <label class="info-label">Form Type</label>
                  <p class="text-sm-light mt-1 uppercase">{{ report.activity_design.form_type_name || report.activity_design.form_type || '---' }}</p>
                </div>
                <div class="full-width-info">
                  <label class="info-label">GAD Mandate</label>
                  <p class="text-sm-light mt-1">{{ report.activity_design.gad_mandate || '---' }}</p>
                </div>
                <div class="full-width-info">
                  <label class="info-label">Gender Issues</label>
                  <p class="text-sm-light mt-1">{{ report.activity_design.gender_issue || '---' }}</p>
                </div>'''

ar_budget_regex = re.compile(
    r'const items = \[\s*\{\s*name:\s*\'Meals and Snacks \(AM/PM\)\',[^\]]*\];',
    re.DOTALL
)

new_ar_budget_logic = r'''const catering = Number(ad.meals_and_snacks || 0) + Number(ad.accommodation || 0);
  const venue = Number(ad.function_room_venue || 0) + Number(ad.equipment_rental || 0) + Number(ad.transportation || 0);
  const program = Number(ad.professional_fee_honoria || 0) + Number(ad.tokens || 0);
  const materials = Number(ad.materials_and_supplies || 0);

  const items = [
    { name: 'Catering & Hospitality', total: catering },
    { name: 'Venue & Logistics', total: venue },
    { name: 'Program & Speakers', total: program },
    { name: 'Materials & Miscellaneous', total: materials }
  ];'''

for d in dirs:
    for f in files:
        file_path = os.path.join(views_dir, d, f)
        if not os.path.exists(file_path):
            continue
        
        with open(file_path, 'r', encoding='utf-8') as file:
            content = file.read()
            
        changed = False
        
        if 'AD' in f:
            if grid_regex.search(content):
                content = grid_regex.sub(new_grid_content, content)
                changed = True
            if budget_regex.search(content):
                content = budget_regex.sub(new_budget_logic, content)
                changed = True
                
        if 'AR' in f:
            if ar_grid_regex.search(content):
                content = ar_grid_regex.sub(new_ar_grid_content, content)
                changed = True
            if ar_budget_regex.search(content):
                content = ar_budget_regex.sub(new_ar_budget_logic, content)
                changed = True
                
        if changed:
            with open(file_path, 'w', encoding='utf-8') as file:
                file.write(content)
            print('Updated', file_path)
