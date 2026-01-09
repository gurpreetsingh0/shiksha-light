@extends('admin.layouts.main')
@section('title','Order Detail')

@push('head')
<style>
    
</style>
@endpush

{{-- order_detail --}}


@section('content')
 <div class="container-fluid mt-4">

  <!-- PAGE HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
      <i class="ik ik-shopping-cart mr-2"></i> Order Details
    </h4>
    <a href="#" class="btn btn-secondary btn-sm">
      <i class="ik ik-arrow-left"></i> Back
    </a>
  </div>

  <!-- ORDER SUMMARY -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">
      Order Summary
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-3"><strong>Order ID:</strong> {{$order_detail[0]->id}}</div>
        <div class="col-md-3"><strong>Date:</strong> {{$order_detail[0]->created_at}}</div>
        <div class="col-md-3"><strong>Status:</strong>
          <span class="badge badge-success">{{$order_detail[0]->order->order_status}}</span>
        </div>
        <div class="col-md-3"><strong>Payment:</strong>
          <span class="badge badge-info">Paid</span>
        </div>
      </div>
    </div>
  </div>

  <!-- CUSTOMER & PAYMENT -->
  <div class="row">

    <!-- CUSTOMER DETAILS -->
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">Customer Details</div>
        <div class="card-body">
          <p><strong>Name:</strong> John Doe</p>
          <p><strong>Email:</strong> john@example.com</p>
          <p><strong>Phone:</strong> +91 98765 43210</p>
          <p><strong>Address:</strong> New Delhi, India</p>
        </div>
      </div>
    </div>

    <!-- PAYMENT DETAILS -->
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">Payment Details</div>
        <div class="card-body">
          <p><strong>Method:</strong> Razorpay</p>
          <p><strong>Transaction ID:</strong> TXN982374</p>
          <p><strong>Total Amount:</strong> ₹3,450</p>
          <p><strong>Payment Status:</strong>
            <span class="badge badge-success">Success</span>
          </p>
        </div>
      </div>
    </div>

  </div>

  <!-- ORDER ITEMS -->
  <div class="card mb-4">
    <div class="card-header">Order Items</div>
    <div class="card-body p-0">
      <table class="table table-bordered mb-0">
        <thead class="thead-light">
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>LED Panel Light</td>
            <td>2</td>
            <td>₹1,200</td>
            <td>₹2,400</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Smart Bulb</td>
            <td>1</td>
            <td>₹1,050</td>
            <td>₹1,050</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ORDER TOTAL -->
  <div class="card mb-4">
    <div class="card-body text-right">
      <p><strong>Subtotal:</strong> ₹3,450</p>
      <p><strong>Tax:</strong> ₹0</p>
      <h5><strong>Grand Total:</strong> ₹3,450</h5>
    </div>
  </div>

  <!-- ACTION BUTTONS -->
  <div class="text-right mb-5">
    <button class="btn btn-primary">
      <i class="ik ik-printer"></i> Print Invoice
    </button>
    <button class="btn btn-danger">
      <i class="ik ik-x-circle"></i> Cancel Order
    </button>
  </div>

</div>

@endsection
