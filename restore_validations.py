import os
import json
import shutil
import urllib.parse
from datetime import datetime, timezone

# Target cutoff time: 2026-06-29 07:15:00 UTC (End of previous conversation)
cutoff_ms = 1782717300000

# Last git commit time
last_commit_ms = 1782628469000

print(f"Looking for files modified between {last_commit_ms} and {cutoff_ms}")

appdata = os.environ.get('APPDATA')
history_dirs = [
    os.path.join(appdata, 'Antigravity IDE', 'User', 'History'),
    os.path.join(appdata, 'Code', 'User', 'History'),
    os.path.join(appdata, 'Cursor', 'User', 'History')
]

workspace_root = r'c:\xampp\htdocs\GAD_AMS_2'

restored_count = 0
restored_files = []

for h_dir in history_dirs:
    if not os.path.exists(h_dir):
        continue
    for root, dirs, files in os.walk(h_dir):
        if 'entries.json' in files:
            entries_path = os.path.join(root, 'entries.json')
            try:
                with open(entries_path, 'r', encoding='utf-8') as f:
                    data = json.load(f)
            except Exception:
                continue
                
            original_path = data.get('resource')
            
            if not original_path:
                continue
                
            # Strip file:/// protocol
            if original_path.startswith('file:///'):
                original_path = original_path[8:] # remove file:///
                original_path = urllib.parse.unquote(original_path)
                original_path = original_path.replace('/', '\\')
                
            # Check if file belongs to our workspace
            if not original_path.lower().startswith(workspace_root.lower()):
                continue
                
            # Find the latest entry BEFORE the cutoff
            best_entry = None
            for entry in data.get('entries', []):
                ts = entry.get('timestamp')
                if ts and ts < cutoff_ms:
                    if best_entry is None or ts > best_entry.get('timestamp'):
                        best_entry = entry
                        
            # ONLY restore if the best entry is NEWER than the last git commit
            if best_entry and best_entry.get('timestamp') > last_commit_ms:
                source_file = os.path.join(root, best_entry.get('id'))
                if os.path.exists(source_file):
                    try:
                        os.makedirs(os.path.dirname(original_path), exist_ok=True)
                        shutil.copy2(source_file, original_path)
                        restored_count += 1
                        restored_files.append(original_path)
                    except Exception as e:
                        pass

print(f"Total files correctly restored: {restored_count}")
for f in set(restored_files):
    print(f" - {f}")
