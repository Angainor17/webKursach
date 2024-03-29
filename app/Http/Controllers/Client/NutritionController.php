<?php

namespace App\Http\Controllers\Client;

use App\Http\BusinessModel\BodyType;
use App\Http\BusinessModel\CalendarDay;
use App\Http\BusinessModel\CalendarProduct;
use App\Http\BusinessModel\CategoryType;
use App\Http\BusinessModel\NotificationType;
use App\Http\BusinessModel\NSBodyTypeListItem;
use App\Http\BusinessModel\NSListItem;
use App\Http\BusinessModel\NSPortionItem;
use App\Http\BusinessModel\PortionType;
use App\Http\Controllers\Controller;
use App\Http\DBModel\Meal;
use App\Http\DBModel\MealUser;
use App\Http\DBModel\Order;
use App\Http\DBModel\OrderProduct;
use App\Http\DBModel\Product;
use App\Http\DBModel\ProductTypeStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NutritionController extends Controller
{
    public function getView()
    {
        return view(
            "client.nutrition",
            [
//                "lol" => mp($this->getDays())
            ]
        );
    }

    public function getList()
    {
        return json_encode($this->getDays());
    }

    public function getStrategyList()
    {
        $mainList = [];
        $mainTable = ProductTypeStrategy::all();

        foreach ($mainTable as $item) {
            $mainListItem = new NSListItem;

            $mainListItem->title = CategoryType::toString($item->productType);
            $mainListItem->titleId = $item->productType;
            $mainListItem->trainingTypeId = $item->trainingType;
            $mainListItem->ageFrom = $item->ageFrom;
            $mainListItem->ageTo = $item->ageTo;

            $bodyTypesList = $item->bodyStrategys()->get();
            foreach ($bodyTypesList as $bodyTypesListItem) {
                $newBodyType = new NSBodyTypeListItem;
                $newBodyType->typeId = $bodyTypesListItem->body_type;
                $newBodyType->typeTitle = BodyType::toString($bodyTypesListItem->body_type);

                $portions = $bodyTypesListItem->portions()->get();
                foreach ($portions as $portion) {
                    $newPortion = new NSPortionItem();
                    $newPortion->size = $portion->size;
                    $newPortion->typeId = $portion->type;
                    $newPortion->type = PortionType::toString($portion->type);
                    $newBodyType->portion[$portion->type] = $newPortion;
                }

                array_push($mainListItem->bodyTypes, $newBodyType);
            }
            array_push($mainList, $mainListItem);
        }
        return $mainList;
    }

    public function addMeal(Request $request)
    {
        $mealsCount = Auth::user()->meals()
            ->get()->where('product_id', '=', $request->id)->count();
        if ($mealsCount > 0) {
            $item = Auth::user()->meals()->get()->where('product_id', '=', $request->id)->first();
            $item->portions_used = +1;//$item->portions_user + Product::where('id', '=', $request->id)->first()->portionSize;
            $item->save();
        } else {
            $meal = new Meal;
            $meal->product_id = $request->id;
            $meal->portions_used = Product::where('id', '=', $request->id)->first()->portionSize;
            $meal->save();

            $mealUser = new MealUser;
            $mealUser->meal_id = $meal->id;
            $mealUser->user_id = Auth::user()->id;
            $mealUser->save();
        }
    }

    public function getDays()
    {
        $products = $this->getProductsData();
        $user = Auth::user();
        $strategys = $this->getStrategyList();

        $calendarDays = [];

        for ($dayInt = 0; $dayInt <= 30; $dayInt++) {
            $calendarDay = new CalendarDay;

            $nextDay = mktime(0, 0, 0, date("m"), date("d") + $dayInt, date("Y"));
            $calendarDay->dateString = date("d.m.y", $nextDay);
            $calendarDay->weekDay = date("l", $nextDay);
            $calendarDay->weekDayInt = date('N', strtotime($calendarDay->weekDay));

            if (strpos($user->trainingSchedule, $calendarDay->weekDayInt) !== false) {

                foreach ($products as $productKey => $product) {
                    foreach ($strategys as $strategy) {
                        if ($strategy->titleId == $product->category) {
                            $newNotification = new NotificationType;
                            $newNotification->productId = $product->productId;
                            $iId = $product->product->imageId;

                            $newNotification->imageId = asset("/uploads/" . $iId);
                            if ($product->portionsLast > 0) {
                                $newNotification->type = 1;

                                $ageValid = ($strategy->ageTo <= $product->product->ageTo) && ($strategy->ageFrom >= $product->product->ageFrom);
                                $trainingValid = true;//strpos($strategy->trainingTypeId, Auth::user()->trainingType) !== false;

                                if ($ageValid && $trainingValid) {
                                    $newNotification->text = "Примите " . $product->portionSize . " " . PortionType::toString($product->portionType);
                                    array_push($calendarDay->notification, $newNotification);
                                }
                            } else {
                                $newNotification->type = 0;
                                $newNotification->text = "Продукт закончился";
                                unset($products[$productKey]);
                                array_push($calendarDay->notification, $newNotification);
                            }
                        }
                    }
                }
            }
            array_push($calendarDays, $calendarDay);
        }
        return $calendarDays;
    }

    public function getProductsData()
    {
        $userId = Auth::user()->id;
        $allOrders = Order::where('userId', '=', $userId)->get();
        $allProducts = [];

        foreach ($allOrders as $order) {
            $allProductsId = OrderProduct::where('order_id', '=', $order->id)->get();

            foreach ($allProductsId as $allProductsIdItem) {
                $isChecked = false;
                $key = -1;

                foreach ($allProducts as $key1 => $value) {
                    if ($value->productId == $allProductsIdItem->product_id) {
                        $isChecked = true;
                        $key = $key1;
                    }
                }

                if ($isChecked) {
                    $allProducts[$key]->productsTotal = $allProducts[$key]->productsTotal + $allProductsIdItem->amount;
                } else {
                    $newItem = new CalendarProduct;
                    $newItem->productId = $allProductsIdItem->product_id;
                    $newItem->productsTotal = $allProductsIdItem->amount;
                    array_push($allProducts, $newItem);
                }
            }
        }

        foreach ($allProducts as $product) {
            $product->product = Product::where('id', '=', $product->productId)->get()->first();
            $product->portionsLast = $product->productsTotal * $product->product->portionTotal;
            $product->portionSize = $product->product->portionSize;
            $product->portionType = $product->product->portionType;
            $product->category = $product->product->category;
        }

        $meals = Auth::user()->meals()->get();
        foreach ($meals as $meal) {
            foreach ($allProducts as $product) {
                if ($meal->product_id == $product->productId) {
                    $product->portionsLast = $product->portionsLast - $meal->portions_used;
                    if ($product->portionsLast < 0) {
                        $product->portionsLast = 0;
                    }
                }
            }
        }
        return $allProducts;
    }
}
