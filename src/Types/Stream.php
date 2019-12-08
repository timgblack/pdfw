<?php
namespace pdfw\Types;
use pdfw\Types\Stream\Write;
use pdfw\Types\Stream\Draw;

class Stream extends PDFObject {
  private $value;
  private $dictionary;
  private $compressed;

  public function __construct($value = '', Dictionary $dictionary = null, $compressed = false) {
    $this->SetValue($value);
    $this->dictionary = $dictionary ?: new Dictionary();
    $this->compressed = $compressed;
  }

  public function GetValue() {
    return $this->value;
  }

  public function SetValue($value) {
    $this->value = $value;
  }

  public function GetDictionary() {
    return $this->dictionary;
  }

  public function SetDictionary($dictionary) {
    $this->dictionary = $dictionary;
  }

  public function __toString() {
    $value = rtrim($this->value, "\n");
    $dict = $this->dictionary;
    if ($this->compressed) {
      $dict['Filter'] = new Name('FlateDecode');
      $value = gzcompress($value);
    }
    $dict['Length'] = strlen($value);
    return (string)$dict . "\nstream\n" . $value . "\nendstream";
  }
}
