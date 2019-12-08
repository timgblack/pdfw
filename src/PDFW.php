<?php
namespace pdfw;
use pdfw\Types\Dictionary;
use pdfw\Types\Indirect;
use pdfw\Types\Name;
use pdfw\Types\PDFArray;
use pdfw\Types\PDFObject;
use pdfw\Types\PDFString;

class PDFW {
  public const VERSION = '1.0.0';

  private $objects = [];
  private $pages = [];
  private $fonts = [];

  private $root;
  private $contents;
  private $info;

  public function __construct() {
    $this->contents = new Dictionary([
      'Type' => new Name('Pages'),
      'Kids' => new PDFArray($this->pages),
      'Count' => count($this->pages),
      'Resources' => new Dictionary ([
        /*'ProcSet' => new PDFArray([
          new Name('PDF'),
          new Name('Text'),
          new Name('ImageB'),
          new Name('ImageC'),
          new Name('ImageI'),
        ]),*/
        'Font' => new Dictionary([])
      ]),
    ]);
    $this->root = new Dictionary([
      'Type' => new Name('Catalog'),
      'Pages' => new Indirect($this->contents, $this),
    ]);
    
    $this->info = new Dictionary([
      'Producer' => new PDFString('Trident PDFW ' . static::VERSION),
    ]);
    $this->AddObject($this->root);
    $this->AddObject($this->contents);
    $this->AddObject($this->info);
  }

  function WriteToStream($stream) {
    return fwrite($stream, $this->GetPDF());
  }

  function WriteToFile($file) {
    $file = fopen($file, 'wb');
    return $this->WriteToStream($file);
  }

  function GetPDF() {
    $pdf = $this->GetHeader();
    for($id = 1; $id <= count($this->objects); $id++) {
      $object = $this->objects[$id];
      $pdf .= sprintf("%d 0 obj\n%s\nendobj\n", $id, (string)$object);
    }
    $pdf .= $this->GetTrailer();
    return $pdf;
  }

  public function GetContents() {
    return $this->contents;
  }

  public function AddObject(PDFObject $object) {
    $index = $this->GetIndirectIndex($object);
    if ($index !== false) {
      return $index;
    }
    $index = count($this->objects) + 1;
    $this->objects[$index] = $object;
    return $index;
  }

  public function GetPages() {
    return $this->pages;
  }

  public function AddPage($pageSize = Page::A4) {
    $page = new Page($this, $pageSize);
    $pageRef = new Indirect($page, $this);

    $this->pages[] = $pageRef;
    $this->AddObject($page);

    $this->contents['Kids']->SetValue($this->pages);
    $this->contents['Count'] = count($this->pages);

    return $page;
  }

  public function GetFonts() {
    return $this->fonts;
  }

  public function AddFont($name, $bold = false, $oblique = false) {
    $font = new Font($name, $bold, $oblique);
    $index = count($this->fonts) + 1;
    $this->fonts[$index] = $font;
    $this->AddObject($font);
    $fontName = 'F' . $index;
    $font->SetName($fontName);
    $this->contents->Resources->Font[$fontName] = new Indirect($font, $this);
    return $font;
  }

  public function GetIndirectIndex($object) {
    return array_search($object, $this->objects);
  }

  private function GetHeader() {
    return "%PDF-1.7\n%π✓™\n";
  }

  private function GetTrailer() {
    $trailer = sprintf("xref\n0 %d\n0000000000 65535 f \n", count($this->objects));
    $offset = strlen($this->GetHeader());
    foreach($this->objects as $id => $object) {
      $trailer .= sprintf("%010d 00000 n \n", $offset);
      $offset += strlen((string)$object);
    }
    $trailer .= sprintf("trailer\n%1\$s\nstartxref\n%2\$d\n%%%%EOF", new Dictionary([
      'Root' => new Indirect($this->root, $this),
      'Info' => new Indirect($this->info, $this),
      'Size' => count($this->objects)
    ]), $offset);
    return $trailer;
  }

}
