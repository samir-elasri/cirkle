<?php

namespace App\Models\Core;

use App\Models\Translations\ListEmailTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\ListEmail
 *
 * @property int $id
 * @property string|null $label
 * @property string|null $send_name
 * @property string|null $send_email
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read Collection<int, Target> $targets
 * @property-read int|null $targets_count
 * @property-read ListEmailTranslation|null $translation
 * @property-read Collection<int, ListEmailTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $object
 * @property string|null $content
 * @method static Builder|Model active()
 * @method static Builder|ListEmail listsTranslations(string $translationField)
 * @method static Builder|ListEmail newModelQuery()
 * @method static Builder|ListEmail newQuery()
 * @method static Builder|ListEmail notTranslatedIn(?string $locale = null)
 * @method static Builder|ListEmail orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|ListEmail orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|ListEmail orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|ListEmail query()
 * @method static Builder|ListEmail translated()
 * @method static Builder|ListEmail translatedIn(?string $locale = null)
 * @method static Builder|ListEmail whereActive($value)
 * @method static Builder|ListEmail whereCreatedAt($value)
 * @method static Builder|ListEmail whereId($value)
 * @method static Builder|ListEmail whereLabel($value)
 * @method static Builder|ListEmail whereSendEmail($value)
 * @method static Builder|ListEmail whereSendName($value)
 * @method static Builder|ListEmail whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|ListEmail whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|ListEmail whereUpdatedAt($value)
 * @method static Builder|ListEmail withTranslation()
 * @mixin Eloquent
 */
class ListEmail extends Model implements TranslatableContract
{

	public string $order_default = 'title';
	public string $order_direction = 'ASC';


	use Translatable;

	protected $fillable = [
		'label',
		'object',
		'send_name',
		'send_email',
		'title',
		'content',
		'active',
	];

	public $translatedAttributes = [
		'title',
		'object',
		'content',
	];


	protected array $niceNames = [
		'object'       => 'Objet du courriel',
		'intern_title' => 'Titre interne de l’envoi',
		'send_name'    => 'Nom qui envoie',
		'send_email'   => 'Adresse courriel qui envoie',
		'title'        => 'Titre ',
		'content'      => 'Contenu ',
	];

	protected array $enum = [
		'type' => [
			'monthly'   => 'Infolettre mensuelle ',
			'formation' => 'Infolettre de formation',
		],
	];

	protected array $customFields = [
		'content' => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => 150]
		],
	];

	protected array $grid = [
		'title',
		'send_name',
	];

//	public function segments()
//	{
//		return $this->hasMany('Segment');
//	}

	public function targets()
	{
		return $this->hasMany(Target::class);
	}
}
