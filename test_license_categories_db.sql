-- Test 1: UPDATE SQL direct pour vérifier que la DB accepte les données JSON
UPDATE drivers 
SET license_categories = '["B", "C", "D"]'::json 
WHERE id = 10;

-- Vérifier le résultat
SELECT id, first_name, last_name, license_categories 
FROM drivers 
WHERE id = 10;
