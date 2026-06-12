<?php

Blade::directive('ifyield', function ($expression) {
	return "<?php if(!empty(trim(\$__env->yieldContent({$expression})))) : ?>";
});