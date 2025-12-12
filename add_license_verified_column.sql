-- Migration SQL pour ajouter la colonne license_verified
-- À exécuter manuellement dans PostgreSQL

-- Vérifier et ajouter la colonne si elle n'existe pas
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 
        FROM information_schema.columns 
        WHERE table_name = 'drivers' 
        AND column_name = 'license_verified'
    ) THEN
        ALTER TABLE drivers 
        ADD COLUMN license_verified BOOLEAN NOT NULL DEFAULT FALSE;
        
        COMMENT ON COLUMN drivers.license_verified IS 'Indique si le permis de conduire a été vérifié';
        
        RAISE NOTICE 'Colonne license_verified ajoutée avec succès';
    ELSE
        RAISE NOTICE 'Colonne license_verified existe déjà';
    END IF;
END $$;

-- Vérifier que la colonne a été ajoutée
SELECT column_name, data_type, column_default, is_nullable
FROM information_schema.columns
WHERE table_name = 'drivers' AND column_name = 'license_verified';
