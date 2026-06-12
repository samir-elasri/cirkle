<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns:xhtml="http://www.w3.org/1999/xhtml">
	<url>
		<loc>{{ url('/') }}</loc>
		<changefreq>weekly</changefreq>
		<priority>0.1</priority>
		<xhtml:link rel="alternate" hreflang="fr" href="{{ url('/fr') }}" />
		<xhtml:link rel="alternate" hreflang="en" href="{{ url('/en') }}" />
	</url>
	@foreach(\App\Models\Core\Page::all() as $item)
		<url>
			<loc>{{ url($item->getUrl('fr')) }}</loc>
			<changefreq>weekly</changefreq>
			<priority>0.1</priority>
			<xhtml:link rel="alternate" hreflang="fr" href="{{ url($item->getUrl('fr')) }}" />
			<xhtml:link rel="alternate" hreflang="en" href="{{ url($item->getUrl('en')) }}" />
		</url>
	@endforeach
	@foreach(\App\Models\Core\News::get() as $item)
		<url>
			<loc>{{ $item->getLocalizedUrl('fr') }}</loc>
			<changefreq>weekly</changefreq>
			<priority>0.1</priority>
			<xhtml:link rel="alternate" hreflang="fr" href="{{ $item->getLocalizedUrl('fr') }}" />
			<xhtml:link rel="alternate" hreflang="en" href="{{ $item->getLocalizedUrl('en') }}" />
		</url>
	@endforeach
</urlset>
