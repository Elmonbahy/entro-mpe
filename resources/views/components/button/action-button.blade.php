@props(['btnType' => 'edit'])

@if ($btnType === 'delete')
  <button type="submit" class="btn btn-info">
    <i class="bi-trash" style="color: #fff"></i>
  </button>
@elseif ($btnType === 'detail')
  <button type="submit" class="btn btn-danger">
    <i class="bi-trash" style="color: #fff"></i>
  </button>
@else
  <button type="submit" class="btn btn-warning">
    <i class="bi-pencil" style="color: #fff"></i>
  </button>
@endif
