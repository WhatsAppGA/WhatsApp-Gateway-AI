@foreach ($phonebooks as $phonebook)
  <option value="{{ $phonebook->id }}">{{ $phonebook->name }} ( {{$phonebook->contacts_count }} Numbers )</option>
@endforeach