<?php

namespace App\Http\Controllers\Import;

use Everyday\HtmlToQuill\Converters\NodeConverterInterface;
use Everyday\HtmlToQuill\HtmlConverter;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

// Обработка табличных данных
class TableConverter implements NodeConverterInterface
{
    public function convert(\DOMNode$node, HtmlConverterInterface $htmlConverter): DeltaOp | array | null
    {
        $ops = [];

        if (!empty(trim($node->textContent))) {
            $ops[] = DeltaOp::embed($node->nodeName, Self::recursiveRowParser($node, $htmlConverter, 0));
        }

        return $ops;
    }
    public function getSupportedTags(): array
    {
        return ['table'];
    }

    private function recursiveRowParser(\DOMNode$node, HtmlConverterInterface $htmlConverter, int $depth)
    {
        $ops = [];

        foreach ($node->childNodes as $child) {

            if ($child->nodeName != "#text" && $child->nodeName != "span") {
                // Если не текст
                $rec_result = Self::recursiveRowParser($child, $htmlConverter, $depth + 1);
                if ($rec_result && ($child->nodeName == 'td' || $child->nodeName == 'th')) {
                    $ops[] = DeltaOp::embed('td', $rec_result);
                } else if ($rec_result && is_array($rec_result) && count($rec_result) !== 0) {
                    if ($child->nodeName != "tr") {
                        $ops = array_merge($ops, $rec_result);
                    } else {
                        $ops[] = DeltaOp::embed($child->nodeName, $rec_result);
                    }
                }
            } else {
                // Если текст
                $dry_parse = $htmlConverter->convertChildren($child);
                if ($dry_parse !== null && is_array($dry_parse) && count($dry_parse) !== 0) {
                    $multi_insert = "";
                    foreach ($dry_parse as $child_op) {
                        $multi_insert .= $child_op->getInsert();
                    }
                    $wet_parse = DeltaOp::text($multi_insert);

                    $ops[] = $wet_parse;
                }
            }

            $multi_insert = "";
            $embeds = false;

            if ($ops && is_array($ops) && count($ops) !== 0) {
                foreach ($ops as $op) {
                    if ($op->isEmbed()) {
                        $embeds = true;
                        break;
                    } else {
                        $multi_insert .= $op->getInsert();
                    }
                }

                if (!$embeds) {
                    $wet_parse = DeltaOp::text($multi_insert);
                    $ops = [$wet_parse];
                }
            }
        }

        return $ops;
    }
}

class Import_ConverterController extends HtmlConverter
{
    public function __construct()
    {
        parent::__construct(); // We want our default converters.
        $this->converters[] = new TableConverter();
    }
}
