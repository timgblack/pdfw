<?php
namespace pdfw\Types;

class Null extends PDFObject {
  public function __construct() { }

  public function __toString() {
    return 'null';
  }
}