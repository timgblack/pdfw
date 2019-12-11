<?php
namespace pdfwext\Stream;
use chillerlan\QRCode\QRCode;
use pdfw\Types\PDFString;
use pdfw\Types\Stream;

class Draw {
  private $stream;

  private $buffer = '';

  private $isTextBlock = false;

  private $curX = 0;
  private $curY = 0;

  private $font;
  private $fontSize;

  public function __construct(Stream $stream) {
    $this->stream = $stream;
  }

  public function Commit() {
    $contents = $this->stream->GetValue();
    $contents .= $this->buffer;
    $this->stream->SetValue($contents);
    $this->Clear();
  }

  public function Clear() {
    $this->buffer = '';
  }

  public function AppendRaw(...$values) {
    $this->buffer .= implode(' ', $values) . "\n";
    return $this;
  }

  public function MoveTo($x, $y) {
    $this->curX = $x;
    $this->curY = $y;
    return $this->AppendRaw($x, $y, 'm');
  }

  public function Move($x, $y) {
    return $this->Move($x + $this->curX, $y + $this->curY);
  }

  public function LineTo($x, $y) {
    $this->curX = $x;
    $this->curY = $y;
    return $this->AppendRaw($x, $y, 'l');
  }

  public function Line($x, $y) {
    return $this->LineTo($x + $this->curX, $y + $this->curY);
  }

  public function Rectangle ($x1, $y1, $x2, $y2) {
    return $this->AppendRaw($x1, $y1, $x2, $y2, 're');
  }

  public function QRCode($content, $x, $y, $size) {
    $this->Commit();

    $qr = (new QRCode)->getMatrix($content)->matrix();

    $moduleCount = count($qr);
    $unitSize = $size / $moduleCount;
	
    function needsToPaintRow($row, $startC, $endC) {
      for ($c = $startC; $c <= $endC; $c++) {
        if (!$row[$c])
          return false;
      }
      return true;
    }

    for ($c = 0; $c < $moduleCount; $c++) {
      for ($r = 0; $r < $moduleCount; $r++) {
        if ($qr[$r][$c]) {
          $endC = $c;
          $endR = $r;
          while ($qr[$endR][$endC + 1]) $endC++;
          while (needsToPaintRow($qr[$endR + 1], $c, $endC)) $endR++;
          
          $width = $unitSize * ($endC - $c + 1);
          $height = $unitSize * ($endR - $r + 1);
          $this->Rectangle($x + $unitSize * $c, $y + $size - $height - $unitSize * $r, $width, $height)->Fill();

          for ($paintedR = $r; $paintedR <= $endR; $paintedR++) {
            for ($paintedC = $c; $paintedC <= $endC; $paintedC++) {
              $qr[$paintedR][$paintedC] = false;
            }
          }
        }
      }
    }
    return $this;
  }

  public function CurveTo($x1, $y1, $x2, $y2, $x3, $y3) {
    return $this->AppendRaw($x1, $y1, $x2, $y2, $x3, $y3, 'c');
  }

  public function Circle($x, $y, $r) {
    $k = 0.552;
    return $this->MoveTo($x - $r, $y)
      ->CurveTo(
        $x - $r, $y + $r * $k,
        $x - $r * $k, $y + $r,
        $x, $y + $r
      )
      ->CurveTo(
        $x + $r * $k, $y + $r,
        $x + $r, $y + $r * $k,
        $x + $r, $y
      )
      ->CurveTo(
        $x + $r, $y - $r * $k,
        $x + $r * $k, $y - $r,
        $x, $y - $r
      )
     ->CurveTo(
        $x - $r * $k, $y - $r,
        $x - $r, $y - $r * $k,
        $x - $r, $y
      )
      ->Fill();
  }

  public function Stroke() {
    return $this->AppendRaw('S');
  }

  public function Fill() {
    return $this->AppendRaw('f');
  }

  public function ClosePath() {
    return $this->AppendRaw('h');
  }
}
