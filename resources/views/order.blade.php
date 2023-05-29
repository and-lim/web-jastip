@extends('layout')

@section('mainSection')
<section>
    <div class="hero-header py-5" style="height: 45vh">
        <div class="container mt-4">
            <div class="row col-lg-10 mx-auto">
                <div class="search-title my-3 text-center">
                    <h1 class="fw-bold text-white">Your Order</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="line p-2"></div>

    <div class="container my-5">

        <div class="title">
            <h1 class="fw-bold">Request List</h1>
        </div>

        @foreach ($request_list as $request)
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2">
                        <img src="img/laptop.jpg" class="img-fluid" alt="">
                    </div>
                    <div class="col-lg-9">
                        <h1 class="mb-2">From: {{ $request->fullname }}</h1>
                        <p>Item Name: {{ $request->request_name }}</p>
                        <p>Quantity: {{ $request->request_quantity }}</p>
                        <p>Price: {{ $request->request_price }}</p>
                        <p>Weight: {{ $request->request_weight }}</p>

                        <div class="float-end d-flex gap-2">
                            <form action="/acceptRequest" method="POST">
                                @csrf
                                <input type="hidden" name="request_id" value="{{ $request->id }}">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check"></i>
                                </button>
                            </form>
                            <form action="/rejectRequest" method="POST">
                                @csrf
                                <input type="hidden" name="request_id" value="{{ $request->id }}">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="container my-5">
        <div class="row">
            <div class="title">
                <h1 class="fw-bold">Order List</h1>
            </div>

            @if ($errors->any())
            <div class="alert alert-dark" role="alert" style="outline: none">
                <i class="text-danger mt-1">{{$errors->first()}}</i>
            </div>
            @endif

            @foreach($order_list_header as $order_header)
            <div class="col-lg-12">
                <div class="card p-3">
                    <div class="card p-3 mb-3 shadow">
                        <div class="form-group mb-3 row">
                            <div class="col-lg-2">
                                <p>From</p>
                            </div>
                            <div class="col-lg-10">
                                <p>{{ $order_header->fullname }}</p>
                            </div>
                        </div>

                        <div class="form-group mb-3 row">
                            <div class="col-lg-2">
                                <p>Address</p>
                            </div>
                            <div class="col-lg-10">
                                <p>{{ $order_header->address }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Phone Number</label>
                            <div class="col-lg-10">
                                <input type="text" readonly class="form-control-plaintext " id="" value="{{ $order_header->phone_number }}">
                            </div>
                        </div>

                        <div class="form-group mb-3 row">
                            <div class="col-lg-2">
                                <p>Shipping Type</p>
                            </div>
                            <div class="col-lg-10">
                                <p>{{ $order_header->shipping_name }}</p>
                            </div>
                        </div>

                        @if($order_header->beacukai_pabean)
                        <div class="form-group mb-3 row">
                            <div class="col-lg-2">
                                <p>Beacukai & Pabean</p>
                            </div>
                            <div class="col-lg-10">
                                <p>{{ $order_header->beacukai_pabean }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="items">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 10px"></th>
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Profit</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order_detail_item[$order_header->id] as $order_item)
                                <tr>
                                    <td style="width: 10px;">
                                        <div class="button">
                                            <button type="button" @if ($order_item->item_status == 'cancelled') disabled @endif class="btn btn-danger py-0 px-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header border-none">
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="/cancelBuyItem" method="POST">
                                                            <div class="modal-body">
                                                                <h3 class="fw-bold mb-3">Why Cancel?</h3>
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Select reason</label>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="reason" id="flexRadioDefault1" value="Item Not Available">
                                                                        <label class="form-check-label" for="flexRadioDefault1">
                                                                            Item Not Available
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group mb-3">
                                                                    <label for="" class="form-label">reason</label>
                                                                    <input type="text" class="form-control">
                                                                </div>
                                                            </div>
                                                            @csrf
                                                            <input type="hidden" name="item_id" value="{{ $order_item->item_id }}">
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td scope="row" class="d-flex">
                                        {{-- <div class="form-check">
                                            @if($order_item->item_status != 'cancelled')
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                            @endif
                                            <label class="form-check-label" for="">
                                                {{ $order_item->item_name }}
                                            </label>
                                        </div> --}}
                                        <button class="btn btn-success">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </td>
                                    <td>{{ $order_item->quantity }}</td>
                                    <td>Rp {{ $order_item->item_display_price }}</td>
                                    <td>RP {{ $order_item->profit }}</td>
                                    <td>Rp {{ $order_item->total }}</td>
                                </tr>
                                @endforeach

                                @foreach($order_detail_request[$order_header->id] as $order_request)
                                <tr>
                                    <td style="width: 10px;">
                                        <div class="button">
                                            <button type="button" class="btn btn-danger py-0 px-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header border-none">
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h3 class="fw-bold mb-3">Why Cancel?</h3>
                                                            <div class="form-group mb-3">
                                                                <label for="" class="form-label">reason</label>
                                                                <input type="text" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Select reason</label>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                                                    <label class="form-check-label" for="flexRadioDefault1">
                                                                        Default radio
                                                                    </label>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-primary">Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td scope="row" class="d-flex">
                                        {{-- <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                            <label class="form-check-label" for="">
                                                {{ $order_request->request_name }}
                                            </label>
                                        </div> --}}
                                        <button class="btn btn-success">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </td>
                                    <td>{{ $order_request->quantity }}</td>
                                    <td>Rp {{ $order_request->request_price }}</td>
                                    <td>Rp {{ $order_request->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="border-top: 2px solid black; ">
                                <tr>
                                    <th scope="row">
                                    </th>
                                    <td></td>
                                    <td></td>
                                    <td>23</td>
                                    <td>23</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end">
                        {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#order">
                            Launch demo modal
                        </button>
                            <div class="modal fade" id="order" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirm Order</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure to order this</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-success">Confirm</button>
                                    </div>
                                </div>
                                </div>
                            </div> --}}
                        <button class="btn btn-success px-3">Shipment</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row mt-5">
            <div class="title">
                <h1 class="fw-bold">Shipment List</h1>
            </div>
            <div class="col-lg-12">
                <div class="card p-3">
                    <div class="form-group mb-3 row">
                        <label for="" class="col-sm-2 col-form-label">To</label>
                        <div class="col-lg-10">
                            <input type="text" readonly class="form-control-plaintext " id="" value="User 3">
                        </div>
                    </div>

                    <div class="form-group mb-3 row">
                        <label for="" class="col-sm-2 col-form-label">Address</label>
                        <div class="col-lg-10">
                            <input type="text" readonly class="form-control-plaintext " id="" value="address">
                        </div>
                    </div>

                    <div class="form-group mb-3 row">
                        <label for="" class="col-sm-2 col-form-label">Phone Number</label>
                        <div class="col-lg-10">
                            <input type="text" readonly class="form-control-plaintext " id="" value="1234678">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Shipping Receipt</label>
                        <div class="col-lg-3">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

</section>
@endsection