<?php
namespace pdfwext\Stream;
use pdfw\Types\PDFString;
use pdfw\Types\Stream;

class Write {
  private $stream;

  private $buffer = '';

  private $isTextBlock = false;

  private $curX;
  private $curY;

  private $font;
  private $fontSize;
  private $fontRGB;

  public function __construct(Stream $stream) {
    $this->stream = $stream;
  }

  public function Commit() {
    $this->CloseTextBlock();
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

  public function OpenTextBlock($new = false) {
    if ($this->isTextBlock) {
      if ($new) {
        $this->CloseTextBlock();
      } else {
        return $this;
      }
    }
    $this->isTextBlock = true;
    $this->AppendRaw('BT');
    return $this;
  }

  public function CloseTextBlock() {
    if ($this->isTextBlock) {
      $this->isTextBlock = false;
      $this->AppendRaw('ET');
   //   $this->AppendRaw ( 'Zeroing from '.$this->curX.' '.$this->curY);
      $this->curX = 0;
      $this->curY = 0;
    }
    return $this;
  }

  public function SetFont($font, $fontSize) {
    $this->OpenTextBlock();
    $this->font = $font;
    $this->fontSize = $fontSize;
    $this->AppendRaw((string)$font->GetName(), $fontSize, 'Tf');
    return $this;
  }

  public static function GetFontTextWidth($font, $fontSize, $text) {
    return $font->fontDefinition->CalculateWidth($text) / 1000 * $fontSize;
  }

  public function GetTextWidth($text) {
    return static::GetFontTextWidth($this->font, $this->fontSize, $text);
  }

  public function Write($text, $advanceX = true) {
    $this->OpenTextBlock();
    $value = new PDFString((string)$text);
    $this->AppendRaw((string)$value, 'Tj');
    if ($advanceX) {
      $width = $this->GetTextWidth((string)$text);
      //$this->stream->Draw->Rectangle($this->curX, $this->curY, $width, $this->fontSize)->Stroke()->Commit();
      /*$x = $this->curX;
      foreach(str_split((string)$text) as $ch) {
        $w = $this->GetTextWidth($ch);
        $this->stream->Draw->Rectangle(
          $x, $this->curY,
          $w, $this->fontSize
        )->Stroke()->Commit();
        $x += $w;
      }/**/
      $this->move($width, 0);
      
    }
    return $this;
  }

  public function Move($x, $y) {
    $this->OpenTextBlock();
    $this->curX += $x;
    $this->curY += $y;
    $this->AppendRaw($x, $y, 'Td');
    return $this;
  }

  public function MoveTo($x, $y) {
    return $this->Move($x - $this->curX, $y - $this->curY);
  }

  public function GetFontRGB() {
    return $this->fontRGB;
  }

  public function SetFontRGB($rgb) {
    $this->fontRGB = $rgb;
    return $this->AppendRaw($rgb[0], $rgb[1], $rgb[2], 'rg');
  }
}
