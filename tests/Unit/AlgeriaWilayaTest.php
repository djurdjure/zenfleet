<?php

namespace Tests\Unit;

use App\Models\AlgeriaWilaya;
use App\Models\AlgeriaCommune;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlgeriaWilayaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_algeria_wilaya(): void
    {
        $wilaya = AlgeriaWilaya::create([
            'code' => '16',
            'name_fr' => 'Alger',
            'name_ar' => 'الجزائر',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('algeria_wilayas', [
            'code' => '16',
            'name_fr' => 'Alger',
            'is_active' => true,
        ]);

        $this->assertEquals('16', $wilaya->code);
        $this->assertEquals('Alger', $wilaya->name_fr);
        $this->assertTrue($wilaya->is_active);
    }

    /** @test */
    public function it_has_display_name_accessor(): void
    {
        $wilaya = AlgeriaWilaya::make([
            'code' => '16',
            'name_fr' => 'Alger',
            'name_ar' => 'الجزائر',
        ]);

        $this->assertEquals('Alger', $wilaya->display_name);

        // Test fallback to Arabic name
        $wilayaNoFrench = AlgeriaWilaya::make([
            'code' => '16',
            'name_ar' => 'الجزائر',
        ]);

        $this->assertEquals('الجزائر', $wilayaNoFrench->display_name);

        // Test fallback to code
        $wilayaNoNames = AlgeriaWilaya::make(['code' => '16']);
        $this->assertEquals('16', $wilayaNoNames->display_name);
    }

    /** @test */
    public function it_has_communes_relationship(): void
    {
        $wilaya = AlgeriaWilaya::create([
            'code' => '16',
            'name_fr' => 'Alger',
            'is_active' => true,
        ]);

        $commune = AlgeriaCommune::create([
            'wilaya_code' => '16',
            'name_fr' => 'Alger-Centre',
            'postal_code' => '16000',
            'is_active' => true,
        ]);

        $this->assertTrue($wilaya->communes->contains($commune));
        $this->assertInstanceOf(AlgeriaCommune::class, $wilaya->communes->first());
    }

    /** @test */
    public function it_has_organizations_relationship(): void
    {
        $wilaya = AlgeriaWilaya::create([
            'code' => '16',
            'name_fr' => 'Alger',
            'is_active' => true,
        ]);

        $organization = Organization::factory()->create(['wilaya' => '16']);

        $this->assertTrue($wilaya->organizations->contains($organization));
        $this->assertInstanceOf(Organization::class, $wilaya->organizations->first());
    }

    /** @test */
    public function it_can_scope_by_active(): void
    {
        AlgeriaWilaya::create(['code' => '16', 'name_fr' => 'Alger', 'is_active' => true]);
        AlgeriaWilaya::create(['code' => '31', 'name_fr' => 'Oran', 'is_active' => false]);

        $activeWilayas = AlgeriaWilaya::active()->get();

        $this->assertCount(1, $activeWilayas);
        $this->assertEquals('16', $activeWilayas->first()->code);
    }

    /** @test */
    public function it_can_get_select_options(): void
    {
        AlgeriaWilaya::create(['code' => '16', 'name_fr' => 'Alger', 'is_active' => true]);
        AlgeriaWilaya::create(['code' => '31', 'name_fr' => 'Oran', 'is_active' => true]);
        AlgeriaWilaya::create(['code' => '25', 'name_fr' => 'Constantine', 'is_active' => false]);

        $options = AlgeriaWilaya::getSelectOptions();

        $this->assertCount(2, $options); // Only active ones
        $this->assertArrayHasKey('16', $options);
        $this->assertArrayHasKey('31', $options);
        $this->assertArrayNotHasKey('25', $options); // Inactive
        $this->assertEquals('Alger', $options['16']);
        $this->assertEquals('Oran', $options['31']);
    }

    /** @test */
    public function select_options_are_ordered_by_name(): void
    {
        AlgeriaWilaya::create(['code' => '31', 'name_fr' => 'Oran', 'is_active' => true]);
        AlgeriaWilaya::create(['code' => '16', 'name_fr' => 'Alger', 'is_active' => true]);
        AlgeriaWilaya::create(['code' => '25', 'name_fr' => 'Constantine', 'is_active' => true]);

        $options = AlgeriaWilaya::getSelectOptions();
        $names = array_values($options);

        $this->assertEquals(['Alger', 'Constantine', 'Oran'], $names);
    }
}