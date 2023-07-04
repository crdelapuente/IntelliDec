<div class="d-flex flex-column mb-3">
    <h2 class="mb-1">Título</h2>
    <div class="form-outline flex-fill mb-0 d-flex align-items-center">
        <input name="title" type="text" placeholder="Título del foro" class="form-control" />
    </div>
    @error('title')
    <small style="color: red">* El título es obligatorio</small>
    @enderror
</div>
<div class="d-flex flex-column mb-3">
    <h2 class="mb-1">Descripción</h2>
    <div class="form-outline flex-fill mb-0 d-flex align-items-center">
        <textarea name="content" type="text" placeholder="Descripción del foro" class="w-100"></textarea>
    </div>
    @error('content')
    <small style="color: red">* La descripción es obligatoria</small>
    @enderror
</div>