import os
import re

def remove_dark_mode(directory):
    # Regex to match 'dark:' classes
    # Matches space (optional) + dark: + chars allowed in tailwind classes
    # Handles variants like dark:hover:bg-red-500
    dark_class_pattern = re.compile(r'\s*\bdark:[\w\-\/:\.]+')
    
    # Counter
    files_modified = 0
    total_matches = 0

    print(f"Scanning {directory}...")

    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.blade.php') or file.endswith('.js') or file.endswith('.vue'):
                file_path = os.path.join(root, file)
                
                try:
                    with open(file_path, 'r', encoding='utf-8') as f:
                        content = f.read()
                    
                    # Search and replace
                    new_content, count = dark_class_pattern.subn('', content)
                    
                    if count > 0:
                        # Clean up double spaces created by removal
                        new_content = re.sub(r'\s{2,}', ' ', new_content)
                        
                        with open(file_path, 'w', encoding='utf-8') as f:
                            f.write(new_content)
                        
                        total_matches += count
                        files_modified += 1
                        # print(f"Cleaned {count} dark classes from {file}")
                        
                except Exception as e:
                    print(f"Error processing {file_path}: {e}")

    print(f"\nSummary:")
    print(f"Files modified: {files_modified}")
    print(f"Total 'dark:' classes removed: {total_matches}")

if __name__ == "__main__":
    target_dir = os.path.join(os.getcwd(), 'resources')
    remove_dark_mode(target_dir)
