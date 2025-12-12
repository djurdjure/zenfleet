-- Vérifier la structure de license_categories
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_name = 'drivers' AND column_name = 'license_categories';

-- Vérifier les données actuelles
SELECT id, first_name, last_name, license_categories 
FROM drivers 
WHERE id = 10;
