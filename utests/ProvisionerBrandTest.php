<?php

use PHPUnit\Framework\TestCase;
use FreePBX\modules\Endpointman\Provisioner\ProvisionerBrand;

class ProvisionerBrandTest extends TestCase
{
    protected $provisionerBrand;
    protected $jsonData;
    protected static $f;
    protected static $o;
    protected static $module = 'Endpointman';

    public static function setUpBeforeClass(): void
    {
        self::$f = FreePBX::create();
        self::$o = self::$f->Endpointman;
    }

    protected function setUp(): void
    {
        $this->jsonData = [
            'data' => [
                'brands' => [
                    'name' => 'TestBrand',
                    'brand_id' => 1,
                    'directory' => 'test_directory',
                    'package' => 'test_package',
                    'md5sum' => 'd41d8cd98f00b204e9800998ecf8427e',
                    'last_modified' => '2023-01-01',
                    'changelog' => 'Initial release',
                    'oui_list' => ['00:11:22', '33:44:55'],
                    'family_list' => [
                        [
                            'id' => '1',
                            'name' => 'TestFamily',
                            'directory' => 'test_family_directory',
                            'last_modified' => '2023-01-01'
                        ]
                    ]
                ]
            ]
        ];

        $this->provisionerBrand = new ProvisionerBrand('TestBrand', 'test_directory', $this->jsonData, self::$f, self::$o);
    }

    public function testIsJSONExist()
    {
        $this->assertFalse($this->provisionerBrand->isJSONExist());
    }

    public function testGetJSONFile()
    {
        $this->assertNull($this->provisionerBrand->getJSONFile());
    }

    public function testImportJSON()
    {
        $this->assertTrue($this->provisionerBrand->importJSON($this->jsonData));
    }

    public function testResetAllData()
    {
        $this->provisionerBrand->resetAllData();
        $this->assertEmpty($this->provisionerBrand->getName());
    }

    public function testGetName()
    {
        $this->assertEquals('TestBrand', $this->provisionerBrand->getName());
    }

    public function testGetBrandID()
    {
        $this->assertEquals(1, $this->provisionerBrand->getBrandID());
    }

    public function testGetDirectory()
    {
        $this->assertEquals('test_directory', $this->provisionerBrand->getDirectory());
    }

    public function testGetPackage()
    {
        $this->assertEquals('test_package', $this->provisionerBrand->getPackage());
    }

    public function testGetMD5Sum()
    {
        $this->assertEquals('d41d8cd98f00b204e9800998ecf8427e', $this->provisionerBrand->getMD5Sum());
    }

    public function testGetLastModified()
    {
        $this->assertEquals('2023-01-01', $this->provisionerBrand->getLastModified());
    }

    public function testGetChangelog()
    {
        $this->assertEquals('Initial release', $this->provisionerBrand->getChangelog());
    }

    public function testGetFamilyList()
    {
        $this->assertCount(1, $this->provisionerBrand->getFamilyList());
    }

    public function testGetOUI()
    {
        $this->assertCount(2, $this->provisionerBrand->getOUI());
    }

    public function testGetUpdate()
    {
        $this->assertFalse($this->provisionerBrand->getUpdate());
    }

    public function testSetUpdate()
    {
        $this->provisionerBrand->setUpdate(true, '1.0.1');
        $this->assertTrue($this->provisionerBrand->getUpdate());
        $this->assertEquals('1.0.1', $this->provisionerBrand->getUpdateVersion());
    }

    public function testGenerateJSON()
    {
        $json = $this->provisionerBrand->generateJSON();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('brands', $json['data']);
    }

    // Additional tests
    public function testGetNameRaw()
    {
        $this->assertEquals('test_directory', $this->provisionerBrand->getNameRaw());
    }

    public function testIsSetBrandID()
    {
        $this->assertTrue($this->provisionerBrand->isSetBrandID());
    }

    public function testCountFamilyList()
    {
        $this->assertEquals(1, $this->provisionerBrand->countFamilyList());
    }

    public function testGetFamily()
    {
        $family = $this->provisionerBrand->getFamily('1');
        $this->assertNotNull($family);
        $this->assertEquals('TestFamily', $family->getName());
    }

    public function testCountOUI()
    {
        $this->assertEquals(2, $this->provisionerBrand->countOUI());
    }

    public function testGetLastModifiedMax()
    {
        $this->assertEquals('2023-01-01', $this->provisionerBrand->getLastModifiedMax());
    }

    public function testGetPathPackageFile()
    {
        $this->assertNull($this->provisionerBrand->getPathPackageFile());
    }

    public function testIsExistPackageFile()
    {
        $this->assertFalse($this->provisionerBrand->isExistPackageFile());
    }

    public function testGetPathBrand()
    {
        $this->assertNull($this->provisionerBrand->getPathBrand());
    }

    public function testGetURLBrandJSON()
    {
        $this->assertNull($this->provisionerBrand->getURLBrandJSON());
    }

    public function testGetURLPackage()
    {
        $this->assertNull($this->provisionerBrand->getURLPackage());
    }

    // New tests
    public function testDownloadBrand()
    {
        $this->assertFalse($this->provisionerBrand->downloadBrand(false, true, false));
    }

    public function testDownloadPackage()
    {
        $this->assertFalse($this->provisionerBrand->downloadPackage(false, true));
    }

    public function testExtractPackage()
    {
        $this->assertFalse($this->provisionerBrand->extractPackage(true, true));
    }

    public function testRemovePackageExtract()
    {
        $this->assertFalse($this->provisionerBrand->removePackageExtract());
    }

    public function testRemovePackageFile()
    {
        $this->assertFalse($this->provisionerBrand->removePackageFile(true));
    }

    public function testCheckMD5PackageFile()
    {
        $this->assertFalse($this->provisionerBrand->checkMD5PackageFile(true, true));
    }

    public function testMovePackageExtracted()
    {
        $this->assertFalse($this->provisionerBrand->movePackageExtracted(false, [], true));
    }

    public function testUninstall()
    {
        $this->assertFalse($this->provisionerBrand->uninstall());
    }
}