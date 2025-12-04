<div class="form-group">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $menu->name) }}" class="form-control" required>
</div>

<div class="form-group">
    <label for="location">Location (optional, e.g., header or footer)</label>
    <input type="text" name="location" id="location" value="{{ old('location', $menu->location) }}" class="form-control" placeholder="header, footer, etc.">
    <small class="text-muted">Location helps auto-wire header/footer menus.</small>
</div>
