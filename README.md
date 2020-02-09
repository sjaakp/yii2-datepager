yii2-datepager
==============

#### Paging on date values for Yii2 ####
[![Latest Stable Version](https://poser.pugx.org/sjaakp/yii2-datepager/v/stable)](https://packagist.org/packages/sjaakp/yii2-datepager)
[![Total Downloads](https://poser.pugx.org/sjaakp/yii2-datepager/downloads)](https://packagist.org/packages/sjaakp/yii2-datepager)
[![License](https://poser.pugx.org/sjaakp/yii2-datepager/license)](https://packagist.org/packages/sjaakp/yii2-datepager)

These classes enable database paging based on date values 
in the [Yii 2.0](https://yiiframework.com/ "Yii") PHP Framework.

A demonstration of **yii2-datepager** is [here](https://sjaakpriester.nl/software/datepager).

## Installation ##

The preferred way to install **yii2-datepager** is through [Composer](https://getcomposer.org/). 
Either add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-datepager": "*"` 

Or run:

`composer require sjaakp/yii2-datepager "*"` 

You can manually install **yii2-datepager** by
 [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-datepager/archive/master.zip).

## Using Datepager ##

Using **Yii2 Datepager** is easy. A minimum usage scenario would look like 
the following. In `EventController.php` we would have something like:

    <?php
	use sjaakp\datepager\ActiveDataProvider;

	class EventController extends Controller
	{
		// ...

		public function actionIndex()    {
	        $dataProvider = new ActiveDataProvider([
	            'query' => Event::find(),
	            'dateAttribute' => 'date'
	        ]);
	
	        return $this->render('index', [
	            'dataProvider' => $dataProvider
	        ]);
	    }

		// ... more actions ...
	}

The corresponding view file `index.php` could look something like:

    <?php
	use sjaakp\datepager\DatePager;
	?>

    <?= DatePager::widget([
        'dataProvider' => $dataProvider
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'date',
            'title',
            // ... more columns ...
        ],
    ]); ?>


## Classes ##

### ActiveDataProvider ###

This is an extension from its Yii-counterpart in `yii\data`, and can be used in 
the same way. It is important to set attribute `$dateAttribute`.

#### $dateAttribute ####

`string` Set this to the name of the attribute which is used to define the pages. 
Must be set.

#### $interval ####

`string` Defines the interval of the pages. Must be set in a form PHP's 
[`DateInterval`](https://www.php.net/manual/en/dateinterval.construct.php) 
can understand. Default: `'P1Y'` (one year).

#### $beginDate ####

`string` Date of the first **Datepager** page. If not set (default), **Datepager** determines
this date itself. It can be set to any date within the range of the first page.
Format: any PHP [date format](https://www.php.net/manual/en/datetime.formats.date.php).

#### $endDate ####

`string` Date of the last **Datepager** page. If not set (default), **Datepager** determines
this date itself. It can be set to any date within the range of the last page.
Format: any PHP [date format](https://www.php.net/manual/en/datetime.formats.date.php).

#### $dateParam ####

`string` The **Datepager** HTML parameter name. Default value: `'date'`. Might be changed 
if there is a conflict with other functionality.


----------

### DatePager ###

This is the widget that renders the actual datepager. 
The attribute `$dataProvider` must be set.

#### $dataProvider ####

The **Datepager** `ActiveDataProvider` that this pager is associated with. 
Must be set.

#### $maxButtonCount ####

`int` The maximum number of page buttons rendered. Default: `10`.

#### $labelFormat ####

`null|string|callable` Defines the format of the page label.
- If `null`: **DatePager** determines the format based on the dataprovider's interval.
- If `string`: a date format according to Yii's [`Fomrmatter::dateformat`](https://www.yiiframework.com/doc/api/2.0/yii-i18n-formatter#$dateFormat-detail).
- If `callable`: a `function($page, $datePager)`, returning a `string`,
    where `$page` is a PHP [`DateTimeInterface`](https://www.php.net/manual/en/class.datetimeinterface.php).
  

#### $options ####

`array` HTML options for the datepager container tag. 
Default: `[ 'class' => 'pagination' ]`, compatible with Bootstrap. 

#### $buttonOptions ####

`array` HTML options for the datepager buttons. 
Default: `[ 'class' => 'page-item' ]`, compatible with Bootstrap 4. 

#### $linkOptions ####

`array` HTML options for the datepager links. 
Default: `[ 'class' => 'page-link' ]`, compatible with Bootstrap 4. 

#### $activePageCssClass ####

`string` CSS class of the active page. Default: `'active'`.

#### $disabledPageCssClass ####

`string` CSS class of a disabled page. Default: `'disabled'`.

#### $prevPageLabel, $nextPageLabel ####

`'string'` Text labels for the previous and next buttons, will not be HTML encoded.
If `false`, the button will not be rendered. Defaults: `'&laquo;'` and `'&raquo;'`.

#### $firstPageLabel, $lastPageLabel ####

`'string'` Text labels for the first and last buttons, will not be HTML encoded.
If `false` (default), the button will not be rendered.

All properties, except `$dataProvider` and `$labelFormat` are direct equivalents of 
their counterparts in Yii's [LinkPager](https://www.yiiframework.com/doc/api/2.0/yii-widgets-linkpager).
 