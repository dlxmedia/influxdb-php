<?php
namespace Leaseweb\InfluxDB\Test;

use Leaseweb\InfluxDB\ResultSet;

class ResultSetTest extends \PHPUnit_Framework_TestCase
{
    /** @var ResultSet  $resultSet*/
    protected $resultSet;

    public function setUp()
    {
        $resultJsonExample = file_get_contents(dirname(__FILE__) . '/result.example.json');
        $this->resultSet = new ResultSet($resultJsonExample);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionIfJSONisNotValid()
    {
        $invalidJSON = 'foo';

        new ResultSet($invalidJSON);
    }

    /**
     * Throws Exception if something went wrong with influxDB
     * @expectedException \Leaseweb\InfluxDB\InfluxDBClientError
     */
    public function testThrowsInfluxDBException()
    {

        $errorResult = <<<EOD
{
    "series": [],
    "error": "Big error, many problems."
}
EOD;
        new ResultSet($errorResult);
    }

    /**
     * We can get points from measurement
     */
    public function testGetPoints()
    {
        $expectedNumberOfPoints = 3;

        $points = $this->resultSet->getPoints();

        $this->assertTrue(
            is_array($points)
        );

        $this->assertCount($expectedNumberOfPoints, $points);

    }

    /**
     * We can get points from measurement
     */
    public function testGetPointsFromMeasurementName()
    {
        $measurementName = 'cpu_load_short';
        $expectedNumberOfPoints = 2;
        $expectedValueFromFirstPoint = 0.64;

        $points = $this->resultSet->getPoints($measurementName);

        $this->assertTrue(
            is_array($points)
        );

        $this->assertCount($expectedNumberOfPoints, $points);

        $somePoint = array_shift($points);

        $this->assertEquals($expectedValueFromFirstPoint, $somePoint['value']);
    }

    public function testGetPointsFromTags()
    {
        $tags = array("host" => "server01");
        $expectedNumberOfPoints = 2;

        $points = $this->resultSet->getPoints('', $tags);

        $this->assertTrue(is_array($points));
        $this->assertCount($expectedNumberOfPoints, $points);
    }

    public function testGetPointsFromNameAndTags()
    {
        $tags = array("host" => "server01");
        $expectedNumberOfPoints = 2;

        $points = $this->resultSet->getPoints('', $tags);

        $this->assertTrue(is_array($points));
        $this->assertCount($expectedNumberOfPoints, $points);
    }
}