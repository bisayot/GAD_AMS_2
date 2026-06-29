import os, glob

views_dir = r'c:\xampp\htdocs\GAD_AMS_2\frontend\src\views\college'
files = glob.glob(os.path.join(views_dir, '**', '*.vue'), recursive=True)

# Also include CollegeDashboard.vue
files.append(r'c:\xampp\htdocs\GAD_AMS_2\frontend\src\views\CollegeDashboard.vue')

for file_path in files:
    if not os.path.exists(file_path): continue
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    changed = False
    
    if "user.value.role !== 'college'" in content:
        content = content.replace("user.value.role !== 'college'", "!['twg', 'non-twg'].includes(user.value.role)")
        changed = True
        
    if "user.role !== 'college'" in content:
        content = content.replace("user.role !== 'college'", "!['twg', 'non-twg'].includes(user.role)")
        changed = True

    if "user.value.role || 'college'" in content:
        content = content.replace("user.value.role || 'college'", "user.value.role")
        changed = True
        
    if "user.value.role === 'twg' ? '(TWG)' : '(Non-TWG)'" in content:
        pass # Already good, wait let me replace the dashboard logic
        
    if changed:
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print('Updated', file_path)
