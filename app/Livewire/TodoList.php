<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:20')]
    public $name;

    public $search;

    #[Rule('required|min:3|max:20')]
    public $editingTodoName;
    
    public $editingTodoID;

    public function create()
    {
        $validated = $this->validateOnly('name');
        Todo::create($validated);

        $this->reset('name');

        session()->flash('success', 'Created');
    }

    public function destroy($todoID)
    {
        Todo::find($todoID)->delete();
    }

    public function toggle($todoID)
    {
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoID)
    {
        $this->editingTodoName = Todo::find($todoID)->name;
        $this->editingTodoID = $todoID;
    }

    public function cancelEdit()
    {
        $this->reset('editingTodoName', 'editingTodoID');
    }

    public function update()
    {
        $this->validateOnly('editingTodoName');

        Todo::find($this->editingTodoID)->update([
            'name' => $this->editingTodoName,
        ]);

        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()
                ->where('name', 'like', "%{$this->search}%")
                ->paginate(5),
        ]);
    }
}
