<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Models\ProductLocation;
use App\Services\ProductService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Throwable;

class AdministrationController extends Controller {
  protected $productService;
  protected $userService;

  public function __construct(
    ProductService $productService,
    UserService $userService
  ) {
    $this->productService = $productService;
    $this->userService = $userService;
  }

  public function getAllUsers(Request $req) {
    $page = $req->input('page');
    $perPage = $req->input('perPage');
    return response()->json($this->userService->getAllUsers($page, $perPage), 200);
  }

  public function addRoleToUser($userId, $roleId) {
    try {
      $res = $this->userService->setRoleToUser($userId, $roleId);
      extract($res);
    } catch (Throwable $e) {
      report($e);
      $status = 500;
      $message = 'Internal error occurred!';
    }

    return response()->json(['message' => $message], $status);
  }

  public function addOrUpdateQuantities($productId, $locationId, $quantity) {
    $status = 200;
    $message = 'Operation successfully completed!';

    try {
      $this->productService->upsertLocationStock($productId, $locationId, $quantity);
    } catch (NotFoundException $e) {
      report($e);
      $message = $e->getMessage();
      $status = $e->getCode();
    } catch (Throwable $e) {
      report($e);
      $status = 500;
      $message = 'Internal error occurred!';
    }

    return response()->json(['message' => $message], $status);
  }
}
