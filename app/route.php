<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

use think\Route;
//详情
Route::get('info/:id','index/index/info');
//搜索
Route::get('search','index/index/search');
//专业
Route::get('zhuanye','index/index/zhuanye');
//学校
Route::get('school','index/index/school');
//栏目
Route::get('lanmu/:id','index/index/lanmu');
//标签
Route::get('tag/:id/[:cate_id]','index/index/tag');
//api
Route::get('Apirefer','index/index/indexRefer');
//获取省列表接口
// Route::get('province','index/index/getProvince');
// //获取市区接口
// Route::get('city','index/index/getCity');
// 报名页面
Route::get('wsbm','index/register/index');
Route::get('cjcx','index/register/cjcx');
Route::get('kemu','index/register/kemu');
//报名数据接口
Route::get('doRegister','index/register/doRegisterData');
//全站索引
Route::get('sitemap','index/index/sitemap');
Route::get('registration_time','index/index/registration_time');
Route::get('getTestTime','index/index/getTestTime');

