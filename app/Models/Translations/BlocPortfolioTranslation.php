<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocPortfolioTranslation
 *
 * @property int $id
 * @property int $bloc_portfolio_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|BlocPortfolioTranslation newModelQuery()
 * @method static Builder|BlocPortfolioTranslation newQuery()
 * @method static Builder|BlocPortfolioTranslation query()
 * @method static Builder|BlocPortfolioTranslation whereBlocPortfolioId($value)
 * @method static Builder|BlocPortfolioTranslation whereId($value)
 * @method static Builder|BlocPortfolioTranslation whereLocale($value)
 * @method static Builder|BlocPortfolioTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlocPortfolioTranslation extends TranslationModel
{
	public $timestamps = false;
}
