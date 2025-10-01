<?php

namespace Tests\Unit;

use App\Models\Supplier;
use Tests\TestCase;

class AlgerianComplianceTest extends TestCase
{
    public function test_nif_validation_accepts_valid_15_digit_nif()
    {
        $validNifs = [
            '123456789012345',
            '998877665544332',
            '000111222333444'
        ];

        foreach ($validNifs as $nif) {
            $this->assertTrue(Supplier::validateNIF($nif), "NIF {$nif} should be valid");
        }
    }

    public function test_nif_validation_rejects_invalid_nif()
    {
        $invalidNifs = [
            '12345', // Too short
            '12345678901234567890', // Too long
            'abcdefghijklmno', // Non-numeric
            '123456789012ab3', // Contains letters
            '', // Empty
            null // Null
        ];

        foreach ($invalidNifs as $nif) {
            $this->assertFalse(Supplier::validateNIF($nif), "NIF {$nif} should be invalid");
        }
    }

    public function test_nis_validation_accepts_valid_15_digit_nis()
    {
        $validNisList = [
            '098765432109876',
            '111222333444555',
            '999888777666555'
        ];

        foreach ($validNisList as $nis) {
            $this->assertTrue(Supplier::validateNIS($nis), "NIS {$nis} should be valid");
        }
    }

    public function test_nis_validation_rejects_invalid_nis()
    {
        $invalidNisList = [
            '1234', // Too short
            '12345678901234567890', // Too long
            'abcdefghijklmno', // Non-numeric
            '', // Empty
            null // Null
        ];

        foreach ($invalidNisList as $nis) {
            $this->assertFalse(Supplier::validateNIS($nis), "NIS {$nis} should be invalid");
        }
    }

    public function test_trade_register_validation_accepts_valid_formats()
    {
        $validRegisters = [
            '16/00-1234567', // Alger format
            '31/99-9876543', // Oran format
            '06/24-5555555', // Béjaïa current year
            '25/01-1111111', // Constantine format
            '48/23-9999999'  // Ghardaia format
        ];

        foreach ($validRegisters as $register) {
            $this->assertTrue(Supplier::validateTradeRegister($register), "Trade register {$register} should be valid");
        }
    }

    public function test_trade_register_validation_rejects_invalid_formats()
    {
        $invalidRegisters = [
            '16001234567', // Missing separators
            '16/1234567', // Missing year
            '99/00-1234567', // Invalid wilaya code
            '16/100-1234567', // Invalid year format
            '16/00-12345', // Too short number
            '16/00-12345678', // Too long number
            'abc/00-1234567', // Non-numeric wilaya
            '16/ab-1234567', // Non-numeric year
            '16/00-abcdefg', // Non-numeric registration
            '', // Empty
            null // Null
        ];

        foreach ($invalidRegisters as $register) {
            $this->assertFalse(Supplier::validateTradeRegister($register), "Trade register {$register} should be invalid");
        }
    }

    public function test_rib_validation_accepts_valid_20_digit_rib()
    {
        $validRibs = [
            '12345678901234567890',
            '00011122233344455566',
            '99988877766655544433'
        ];

        foreach ($validRibs as $rib) {
            $this->assertTrue(Supplier::validateRIB($rib), "RIB {$rib} should be valid");
        }
    }

    public function test_rib_validation_rejects_invalid_rib()
    {
        $invalidRibs = [
            '123456789', // Too short
            '123456789012345678901', // Too long
            'abcdefghijklmnopqrst', // Non-numeric
            '1234567890123456789a', // Contains letter
            '', // Empty
            null // Null
        ];

        foreach ($invalidRibs as $rib) {
            $this->assertFalse(Supplier::validateRIB($rib), "RIB {$rib} should be invalid");
        }
    }

    public function test_wilaya_codes_validation()
    {
        $validWilayas = [
            'Adrar', 'Chlef', 'Laghouat', 'Oum El Bouaghi', 'Batna',
            'Béjaïa', 'Biskra', 'Béchar', 'Blida', 'Bouira',
            'Tamanrasset', 'Tébessa', 'Tlemcen', 'Tiaret', 'Tizi Ouzou',
            'Alger', 'Djelfa', 'Jijel', 'Sétif', 'Saïda',
            'Skikda', 'Sidi Bel Abbès', 'Annaba', 'Guelma', 'Constantine',
            'Médéa', 'Mostaganem', 'MSila', 'Mascara', 'Ouargla',
            'Oran', 'El Bayadh', 'Illizi', 'Bordj Bou Arréridj', 'Boumerdès',
            'El Tarf', 'Tindouf', 'Tissemsilt', 'El Oued', 'Khenchela',
            'Souk Ahras', 'Tipaza', 'Mila', 'Aïn Defla', 'Naâma',
            'Aïn Témouchent', 'Ghardaïa', 'Relizane'
        ];

        foreach ($validWilayas as $wilaya) {
            $this->assertTrue(Supplier::isValidWilaya($wilaya), "Wilaya {$wilaya} should be valid");
        }
    }

    public function test_wilaya_codes_reject_invalid_wilayas()
    {
        $invalidWilayas = [
            'Paris', 'London', 'InvalidWilaya', 'Alger City', 'Constantine2'
        ];

        foreach ($invalidWilayas as $wilaya) {
            $this->assertFalse(Supplier::isValidWilaya($wilaya), "Wilaya {$wilaya} should be invalid");
        }
    }

    public function test_wilaya_code_to_number_conversion()
    {
        $wilayaMapping = [
            'Adrar' => '01',
            'Alger' => '16',
            'Oran' => '31',
            'Constantine' => '25',
            'Ouargla' => '30',
            'Ghardaïa' => '47'
        ];

        foreach ($wilayaMapping as $wilaya => $expectedCode) {
            $this->assertEquals($expectedCode, Supplier::getWilayaCode($wilaya));
        }
    }

    public function test_trade_register_wilaya_code_extraction()
    {
        $testCases = [
            '16/00-1234567' => '16', // Alger
            '31/99-9876543' => '31', // Oran
            '25/01-5555555' => '25', // Constantine
            '06/24-1111111' => '06', // Béjaïa
            '47/23-9999999' => '47'  // Ghardaïa
        ];

        foreach ($testCases as $register => $expectedCode) {
            $extractedCode = Supplier::extractWilayaCodeFromTradeRegister($register);
            $this->assertEquals($expectedCode, $extractedCode, "Should extract wilaya code {$expectedCode} from {$register}");
        }
    }

    public function test_algerian_phone_number_validation()
    {
        $validPhones = [
            '+213-21-123456',    // Landline Alger
            '+213-31-987654',    // Landline Oran
            '+213-555-123456',   // Mobile
            '+213-666-987654',   // Mobile
            '+213-777-111222'    // Mobile
        ];

        foreach ($validPhones as $phone) {
            $this->assertTrue(Supplier::validateAlgerianPhone($phone), "Phone {$phone} should be valid");
        }
    }

    public function test_algerian_phone_number_rejects_invalid()
    {
        $invalidPhones = [
            '+33-1-12345678',    // French number
            '+1-555-123456',     // US number
            '021-123456',        // Missing country code
            '+213-99-123456',    // Invalid area code
            '+213-555-12345',    // Too short mobile
            '+213-555-1234567'   // Too long mobile
        ];

        foreach ($invalidPhones as $phone) {
            $this->assertFalse(Supplier::validateAlgerianPhone($phone), "Phone {$phone} should be invalid");
        }
    }

    public function test_tva_rate_validation()
    {
        $validTvaRates = [19.0, 9.0, 0.0]; // Standard, reduced, exempt

        foreach ($validTvaRates as $rate) {
            $this->assertTrue(Supplier::isValidTvaRate($rate), "TVA rate {$rate}% should be valid");
        }
    }

    public function test_tva_rate_rejects_invalid_rates()
    {
        $invalidTvaRates = [20.0, 15.5, -5.0, 50.0];

        foreach ($invalidTvaRates as $rate) {
            $this->assertFalse(Supplier::isValidTvaRate($rate), "TVA rate {$rate}% should be invalid");
        }
    }

    public function test_complete_supplier_validation()
    {
        $validSupplierData = [
            'company_name' => 'SARL Pièces Auto Alger',
            'nif' => '123456789012345',
            'nis' => '098765432109876',
            'trade_register' => '16/24-1234567',
            'rib' => '12345678901234567890',
            'wilaya' => 'Alger',
            'phone' => '+213-21-123456',
            'tax_rate' => 19.0
        ];

        $this->assertTrue(Supplier::validateCompleteData($validSupplierData));
    }

    public function test_supplier_compliance_score()
    {
        $supplier = new Supplier([
            'nif' => '123456789012345',
            'nis' => '098765432109876',
            'trade_register' => '16/24-1234567',
            'rib' => '12345678901234567890',
            'wilaya' => 'Alger',
            'phone' => '+213-21-123456',
            'certifications' => ['ISO 9001:2015'],
            'tax_rate' => 19.0
        ]);

        $complianceScore = $supplier->calculateComplianceScore();

        // Perfect compliance should score 100%
        $this->assertEquals(100, $complianceScore);
    }

    public function test_supplier_compliance_score_with_missing_data()
    {
        $supplier = new Supplier([
            'nif' => '123456789012345',
            'trade_register' => '16/24-1234567',
            'wilaya' => 'Alger',
            // Missing: NIS, RIB, phone, certifications
        ]);

        $complianceScore = $supplier->calculateComplianceScore();

        // Partial compliance should score less than 100%
        $this->assertLessThan(100, $complianceScore);
        $this->assertGreaterThan(0, $complianceScore);
    }
}