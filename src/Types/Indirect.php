<?php
namespace pdfw\Types;
use pdfw\PDFW;

class Indirect extends PDFObject {
  private $value;
  private $root;

  public function __construct(PDFObject $value, PDFW $root) {
    $this->SetValue($value);
    $this->SetRoot($root);
  }

  public function GetValue() {
    return $this->value;
  }

  public function SetValue(PDFObject $value) {
    $this->value = $value;
  }

  public function GetRoot() {
    return $this->root;
  }

  public function SetRoot($root) {
    $this->root = $root;
    //if ($root->GetIndirectIndex($value) === false) {
    //  $root->AddObject($value);
    //}
  }

  public function __toString() {
    $index = $this->root->GetIndirectIndex($this->value);
    if ($index === false) {
      $index = $this->root->AddObject($this->value);
    }
    return sprintf('%d 0 R', $index);
  }
}