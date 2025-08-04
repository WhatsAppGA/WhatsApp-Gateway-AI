<table id="datatable1" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Number</th>
          
            {{-- <th class="d-flex justify-content-center">Aksi</th> --}}
        </tr>
    </thead>
    <tbody>
       @foreach ($contacts as $contact)
           
       <tr>
           <td>{{$contact->name}}</td>
           <td>{{$contact->number}}</td>
           {{-- <td>
               <div class="d-flex justify-content-center">
                   <button class="btn btn-success btn-sm mx-3">Add to Tag</button>
               </div>
            </td> --}}
        </tr>
        @endforeach
      

    </tbody>
    <tfoot></tfoot>
</table>