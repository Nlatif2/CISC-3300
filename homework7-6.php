<?php
class Car {
    private $make;
    private $model;
    private $year;
    public function __construct($make, $model, $year) {
        $this->make = $make;
        $this->model = $model;
        $this->year = $year;
    }
    public function getMake() {
        return $this->make;
    }
    public function setMake($make) {
        $this->make = $make;
    }
    public function getModel() {
        return $this->model;
    }
    public function setModel($model) {
        $this->model = $model;
    }
    public function getYear() {
        return $this->year;
    }
    public function setYear($year) {
        $this->year = $year;
    }
    public static function carInfo() {
        return "Cars are a mode of transportation with various makes and models.";
    }
}
?>
