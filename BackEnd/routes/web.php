<?php
// 身份验证
Route::group(['prefix' => 'auth'], function () {
	Route::get('login', 'Auth\LoginController@oAuth');
	Route::get('logout', 'Auth\LoginController@logout');
});

// API 接口
Route::group(['prefix' => 'api', 'namespace' => 'Api', 'middleware' => 'token'], function () {
	// 用户相关
	Route::get('users', 'UserController@index');
	Route::get('users/{id}', 'UserController@show');
	// 用户设置相关
	Route::get('usersettings/{id}', 'UserSettingController@show');
	Route::post('usersettings/{id}', 'UserSettingController@setUserSettings');
	// 文章相关
	Route::put('articles/status/{id}', 'ArticleController@toggleStatus');
	Route::get('articles/random/{id}', 'ArticleController@getRandomArticle');
	Route::resource('articles', 'ArticleController');
	// 文章分类相关
	Route::resource('categories', 'CategoryController');
	// 申请译者相关
	Route::resource('applicants', 'ApplicantController');
	Route::post('applicants/check', 'ApplicantController@checkInvitation');
	// 推荐文章相关
	Route::put('recommends/result/{id}/{result}', 'RecommendController@result');
	Route::resource('recommends', 'RecommendController');
	// 翻译文章相关
	Route::post('translations', 'TranslationController@store');
	Route::put('translations/{id}', 'TranslationController@update');
	Route::patch('translations/{id}', 'TranslationController@post');
	Route::post('translations/pr', 'TranslationController@handlePR');
	Route::get('translations/show/{id}', 'TranslationController@show');
	Route::get('translations/pull/{status}', 'TranslationController@index');
	Route::post('translations/claim/review', 'TranslationController@claimReview');
	Route::post('translations/claim/translation', 'TranslationController@claimTranslation');
	// 获取通知相关
	Route::resource('notifications', 'NotificationController');
	// 排行榜相关
	Route::get('statistics', 'StatisticController@index');
	Route::get('statistics/overview', 'StatisticController@overview');
});

// 其他路由
Route::any('{uri}', function($uri)
{
	return view('index', ['user'=>'']);
})->where('uri', '.*?');