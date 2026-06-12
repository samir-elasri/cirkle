@servers(['stage' => 'bootstra@199.115.124.122', 'prod' => 'bootstra@199.115.124.122', 'local' => '127.0.0.1'])

@setup
$now = new DateTime();
$repo = "@mbsg01.mbiance.com:9418/git/v1/base-laravel-6.git";
@endsetup

@task('deploy', ['on' => $on])
@if($user && $password)
	@if($on == 'local')
		rm -dfr envoyTest &&
		mkdir envoyTest &&
		cd envoyTest &&
	@endif
	git init &&
	git pull http://{{$user}}:{{$password}}{{$repo}} &&
	echo 'Deploy: {{$user}} {{$now->format('Y-m-d\TH:i:s.u')}}' >> envoy.log &&
	git status -s | grep -e "^\??" | cut -c 4- >> .gitignore &&
	git add -A &&
	git config user.email "{{$user}}@mbiance.com"
	git config user.name "{{$user}}"
	echo `git commit --message="Deploy update .gitignore"` &&
	git push --set-upstream http://{{$user}}:{{$password}}{{$repo}} master &&
	cat /dev/null > ~/.bash_history &&
	cp .env.{{$on}} .env
	composer install &&
	php artisan migrate:fresh --seed&&
	npm install &&
	{{-- Build semantic ? --}}
	gulp prod
@else
	echo Please enter your git username --user={username} and password --password={password}
@endif
@endtask

{{-- Rollback commits --}}

@task('fresh', ['on' => $on])
cp .env.{{ $env }} .env &&
rm -rf ./vendor &&
rm -f ./composer.lock &&
rm -f ./package-lock.json &&
npm install &&
gulp prod &&
composer install &&
php artisan migrate:fresh --seed &&
php artisan deploy:notification {{ $on }}
@endtask

@task('update', ['on' => $on])
@if($user && $password)
	git pull https://{{$user}}:{{$password}}{{$repo}} &&
	cat /dev/null > ~/.bash_history &&
	echo 'Update: {{$user}} {{$now->format('Y-m-d\TH:i:s.u')}}' >> envoy.log &&
	composer install &&
	npm install &&
	php artisan migrate &&
	gulp prod
	php artisan
@else
	echo Please enter your git username --user={username} and password --password={password}
@endif
@endtask

@task('check', ['on' => $on])
git status
@endtask

{{-- local runs where the envoy file is --}}
@task('test', ['on' => $on])
touch test.txt
@endtask

