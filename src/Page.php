<?php
namespace pdfw;
use pdfw\Types\Dictionary;
use pdfw\Types\Indirect;
use pdfw\Types\Name;
use pdfw\Types\PDFArray;
use pdfw\Types\PDFObject;
use pdfw\Types\PDFString;
use pdfw\Types\Stream;

class Page extends PDFObject {

  public const A4 = [595.276, 841.89001];
  public const USLetter = [];

  public $width;
  public $height;

  private $contents = [];
  private $dictionary;
  private $pdf;

  public function __construct(PDFW $pdf, $pageSize = Page::A4) {
    $this->width = $pageSize[0];
    $this->height = $pageSize[1];
    $this->dictionary = new Dictionary([
      'Type' => new Name('Page'),
      'MediaBox' => new PDFArray([ 0, 0, $pageSize[0], $pageSize[1] ]),
      'Contents' => new Indirect(new Stream(), $pdf),
      'Parent' => new Indirect($pdf->GetContents(), $pdf),
      'Annots' => new PDFArray([])
    ]);
    $this->pdf = $pdf;
  }

  public function AddFont($font) {
    $this->pdf->AddFont($font);
  }

  public function AddLink($url, $boundingBox) {
    $link = new Dictionary([
      'Rect' => new PDFArray($boundingBox),
      'Type' => new Name('Annot'),
      'Subtype' => new Name('Link'),
      'A' => new Dictionary([
        'Type' => new Name('Action'),
        'S' => new Name('URI'),
        'URI' => new PDFString($url)
      ]),
      'P' => new Indirect($this, $this->pdf)
    ]);
    $this->dictionary->Annots[] = $link;
    $this->pdf->AddObject($link);
  }

  public function GetContent() {
    return $this->dictionary->Contents->GetValue();
  }

  public function __toString() {
    return (string)$this->dictionary;
  }
}
