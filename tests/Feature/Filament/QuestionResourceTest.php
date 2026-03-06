<?php

use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Models\Question;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($this->admin);
});

it('can render the list page', function () {
    $questions = Question::factory()->count(3)->create();

    Livewire::test(ListQuestions::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($questions);
});

it('can render the create page', function () {
    Livewire::test(CreateQuestion::class)
        ->assertSuccessful();
});

it('can create an MCQ question', function () {
    Livewire::test(CreateQuestion::class)
        ->fillForm([
            'type' => 'mcq',
            'body' => 'What is the capital of France?',
            'options' => [
                ['text' => 'London'],
                ['text' => 'Paris'],
                ['text' => 'Berlin'],
            ],
            'correct_answer' => 'Paris',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $question = Question::query()->where('correct_answer', 'Paris')->first();
    expect($question)
        ->not->toBeNull()
        ->type->toBe('mcq')
        ->body->toContain('What is the capital of France?');
});

it('can create a true/false question', function () {
    Livewire::test(CreateQuestion::class)
        ->fillForm([
            'type' => 'true_false',
            'body' => 'The sky is blue.',
            'correct_answer' => 'true',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $question = Question::query()->where('type', 'true_false')->first();
    expect($question)
        ->not->toBeNull()
        ->body->toContain('The sky is blue.')
        ->correct_answer->toBe('true');
});

it('validates required fields', function () {
    Livewire::test(CreateQuestion::class)
        ->fillForm([
            'type' => 'mcq',
            'body' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['body']);
});

it('can edit a question', function () {
    $question = Question::factory()->create([
        'type' => 'mcq',
        'body' => 'Old body',
        'correct_answer' => 'Old answer',
    ]);

    Livewire::test(EditQuestion::class, ['record' => $question->getRouteKey()])
        ->fillForm([
            'body' => 'Updated body',
            'correct_answer' => 'Updated answer',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($question->fresh())
        ->body->toContain('Updated body')
        ->correct_answer->toBe('Updated answer');
});

it('can delete a question from edit page', function () {
    $question = Question::factory()->create();

    Livewire::test(EditQuestion::class, ['record' => $question->getRouteKey()])
        ->callAction('delete')
        ->assertRedirect();

    $this->assertDatabaseMissing('questions', ['id' => $question->id]);
});

it('can search questions by body', function () {
    $matching = Question::factory()->create(['body' => 'Capital of France']);
    $nonMatching = Question::factory()->create(['body' => 'Speed of light']);

    Livewire::test(ListQuestions::class)
        ->searchTable('Capital')
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$nonMatching]);
});
