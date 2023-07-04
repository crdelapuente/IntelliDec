<div class="d-flex flex-column mb-3">
    <h2 class="mb-1">Nombre</h2>
    <div class="form-outline flex-fill mb-0 d-flex align-items-center">
        <input name="name" type="text" placeholder="Nombre del proyecto" class="form-control" value="{{ old('name', $project->name) }}" />
    </div>
    @error('name')
    <small style="color: red">* El título es obligatorio</small>
    @enderror
</div>
<div class="d-flex flex-column mb-3">
    <h2 class="mb-1">Descripción</h2>
    <div class="form-outline flex-fill mb-0 d-flex align-items-center">
        <textarea name="description" type="text" placeholder="Descripción del proyecto" class="form-control">{{ old('description', $project->description) }}</textarea>
    </div>
    @error('description')
    <small style="color: red">* La descripción es obligatoria</small>
    @enderror
</div>
