<?php

namespace App\Models\_Templates;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class InstanceModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function format_date($type, $format = "day-month-auto")
    {
        Carbon::setLocale('ru');

        switch ($type) {
            case 'created':
                $date = $this->created_at;
                break;
            case 'published':
                $date = $this->published_at;
                break;
            case 'updated':
            default:
                $date = $this->updated_at;
                break;
        }

        if ($date == null) {
            return false;
        }
        $carbon_date = Carbon::parse($date)->timezone(Config::get('app.timezone'));

        switch ($format) {
            case 'ago':
                $result = $carbon_date->ago();
                break;

            case 'day-month-auto':
            default:
                if ($carbon_date->year != Carbon::now()->year) {
                    $result = $carbon_date->translatedFormat('d F Y');
                } else {
                    $result = $carbon_date->translatedFormat('d M, H:i');
                }

                break;
        }

        return $result;
    }
}
