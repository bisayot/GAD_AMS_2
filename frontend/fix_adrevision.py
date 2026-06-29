import os, re
views_dir = r'c:\xampp\htdocs\GAD_AMS_2\frontend\src\views'
dirs = ['twg', 'staff']
files = ['ADRevision.vue']

grid_regex = re.compile(
    r'(<div class=\"info-item\">\s*<span class=\"info-label\">Office / Unit<\/span>\s*<span class=\"info-value-white\">\{\{\s*formData\.office\s*\}\}<\/span>\s*<\/div>\s*<div class=\"info-item\">\s*<span class=\"info-label\">Form Type<\/span>\s*<select v-model=\"formData\.form_type\" class=\"modal-input select-input\">[\s\S]*?<\/select>\s*<\/div>\s*)<\/div>',
    re.DOTALL
)

new_grid_content = r'''\1  <div class="info-item">
                <span class="info-label">Activity Classification</span>
                <span class="info-value-white">{{ design.activity_classification || '---' }}</span>
              </div>
              <div class="info-item" style="grid-column: span 2;">
                <span class="info-label">GAD Mandate</span>
                <span class="info-value-white">{{ design.gad_mandate || '---' }}</span>
              </div>
              <div class="info-item" style="grid-column: span 2;">
                <span class="info-label">Gender Issues</span>
                <span class="info-value-white">{{ design.gender_issue || '---' }}</span>
              </div>
            </div>'''

for d in dirs:
    for f in files:
        file_path = os.path.join(views_dir, d, f)
        if not os.path.exists(file_path):
            continue
        
        with open(file_path, 'r', encoding='utf-8') as file:
            content = file.read()
            
        changed = False
        
        if grid_regex.search(content):
            content = grid_regex.sub(new_grid_content, content)
            changed = True
            
        if changed:
            with open(file_path, 'w', encoding='utf-8') as file:
                file.write(content)
            print('Updated', file_path)
