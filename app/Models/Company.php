<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pivots\CompanySector as CompanySectorPivot;

/**
 * @property int $id
 * @property string $short_name
 * @property string $full_name
 * @property int|null $region_id
 * @property string $type
 * @property string $city
 * @property string|null $address
 * @property string|null $inn
 * @property string|null $kpp
 * @property int|null $user_id
 * @property string|null $note
 * @property string|null $website
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Email> $emails
 * @property-read int|null $emails_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Phone> $phones
 * @property-read int|null $phones_count
 * @property-read \App\Models\Region|null $region
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sector> $sectors
 * @property-read int|null $sectors_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereInn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereKpp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereWebsite($value)
 * @mixin \Eloquent
 */
class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region_id',
        'city',
        'type_comp',
        'phone',
        'email',
        'user_id'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'company_sector', 'company_id', 'sector_id')
            ->using(CompanySectorPivot::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    // app/Models/Company.php (или сервис)
    public function addSector(int $sectorId): void
    {
        $pivot = new \App\Models\Pivots\CompanySector(['sector_id' => $sectorId]);
        $this->sectors()->save($pivot); // ← создаёт pivot как модель => сработает observer
    }

    public function removeSector(int $sectorId): void
    {
        $this->sectors()->wherePivot('sector_id', $sectorId)->each(function ($rel) {
            $rel->pivot->delete(); // удаляем pivot моделью => сработает observer
        });
    }

    public function setSectors(array $sectorIds): void
    {
        // аккуратно синкаем через модели (события будут)
        $current = $this->sectors()->pluck('sectors.id')->all();
        $toAdd   = array_diff($sectorIds, $current);
        $toDel   = array_diff($current, $sectorIds);

        foreach ($toAdd as $sid) {
            $this->addSector((int)$sid);
        }
        foreach ($toDel as $sid) {
            $this->removeSector((int)$sid);
        }
    }
}
