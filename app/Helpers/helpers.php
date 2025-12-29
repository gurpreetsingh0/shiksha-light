<?php

use App\Models\Client;
use App\Models\Country;
use App\Models\ServiceType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

if (!function_exists('successAlert')) {
  function successAlert()
  {
    if (session()->has('success')) {
      return '
                <div class="custom-alert success-alert alert-timeout">
                    <i class="fa fa-check-circle"></i> ' . session('success') . '
                </div>
            ';
    }
    return '';
  }
}



function getPlanExpireDate($start_date, $duration)
{
  $startDate = Carbon::parse($start_date);
  $years = (int) $duration;
  $expireDate = $startDate->copy()->addYears($years);
  return $expireDate->format('Y-m-d');
}

// function get_service_plan_id($slug)
// {
//   $cleanSlug = Str::slug(trim($slug));
//   $service = ServiceType::where('slug', $cleanSlug)->first();
//   return $service->id;
// }
#we are making dynamic above function




#Check Client Exit Or Not It Will Return True If Exit Else Return False!
// function checkClientExist($email)
// {
//   $email = strtolower($email); // convert to lowercase
//   return Client::where('email', $email)->exists();
// }

//one man army function for find slug id we use is everywhere according to the requirement!
function getIdFromSlug($tables, $slug = 'other')
{
  $cleanSlug = Str::slug(trim($slug));
  $data = DB::table($tables)->where('slug', $cleanSlug)->first();
  return $data ? $data->id : false;
}

#serach
#It Will Return Table Row Where Id Match Else Return False;
function findTableRow($table_name, $search_array, $additional = "")
{  //['name'=>'joban','age'=>18]
  $data = DB::table($table_name)->where($search_array);
  return $data ? $data : false;
}


#store
function storeHelper($table_name, $form_data, $message = "")
{
  $form_data =  $form_data->except('_token');
  $form_data['slug'] = Str::slug($form_data['name'], '-');
  $form_data['status'] = 'active';
  $form_data['created_at'] = now();
  $form_data['updated_at'] = now();
  $store = DB::table($table_name)->insert($form_data);
  if ($store) {
    Session::flash('success', $message . 'Record stored successfully!');
    return redirect()->back();
  } else {
    Session::flash('err', 'Failed to store record.');
    return redirect()->back();
  }
}

#delete 
function deleteHelper($table_name, $id, $message)
{
  $delete = DB::table($table_name)->where('id', $id)->delete();
  if ($delete) {
    Session::flash('success', $message ?: 'Record stored successfully!');
    return redirect()->back();
  } else {
    Session::flash('err', 'Failed to store record.');
    return redirect()->back();
  }
}

#getbyId
function SearchByIdHelper($table_name, $id)
{
  return DB::table($table_name)->where('id', $id)->first();
}

#update;
function updateHelper($table_name, $id, $request, $message)
{
  $err_msg = 'Something Went Wrong!!';
  $form_data = $request->except(['_method', '_token', 'update_id']);
  $update = DB::table($table_name)->where('id', $id)->update($form_data);
  if ($update) {
    Session::flash('success', $message . 'Record stored successfully!');
    return true;
  } else {
    Session::flash('err', $err_msg);
    return false;
  }
}

function getDataHelper($table_name, $where_condition_array)
{
  if (is_array($table_name)) {
    $data = [];
    foreach ($table_name as $table) {
      $data[$table] = getDataHelper($table, $where_condition_array);
    }
    return $data;
  } else {
    return DB::table($table_name)->where($where_condition_array)->get();
  }
}


//format date

if (!function_exists('dmyHelper')) {
  function dmyHelper($date)
  {
    if (!$date) {
      return null; // or return '-' if you want a placeholder
    }

    return Carbon::parse($date)->format('d-m-Y');
  }
}

function MessageFlashHelper($key, $message)
{
  Session::flash($key, $message);
}
