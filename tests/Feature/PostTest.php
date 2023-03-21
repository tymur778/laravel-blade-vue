<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function test_blog_page_can_be_rendered()
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
    }

    public function test_blog_post_can_be_created()
    {
        $data = [
            'title' => Factory::create()->sentence(),
            'content' => Factory::create()->paragraph(),
        ];

        $response = $this->post('/blog', $data);
        $response->assertRedirect('/login');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/blog', $data);

        $response->assertStatus(302);
        $response->assertRedirect('/blog');

        $this->assertDatabaseHas('posts', [
            'title' => $data['title'],
            'content' => $data['content'],
            'user_id' => $user->id,
        ]);

        $data = [
            'title' => null,
            'content' => null,
        ];

        $response = $this->post('/blog', $data);
        $response->assertSessionHasErrors('title');

        $this->expectException(QueryException::class);
        Post::create($data);
    }

    public function test_blog_post_can_be_edited()
    {
        //model editing
        $post = Post::factory()->create();

        $newTitle = Factory::create()->sentence();
        $newContent = Factory::create()->paragraph();

        $post->title = $newTitle;
        $post->content = $newContent;

        $post->save();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $newTitle,
            'content' => $newContent,
        ]);

        //checking for a null exception
        $post->title = null;
        $post->content = null;

        try {
            $post->save();
            $this->expectException(QueryException::class);
        } catch (QueryException $e) {
            $this->assertSame('23000', $e->getCode());
        }

        //checking put method
        $user = User::factory()->create();

        $newTitle = Factory::create()->sentence();
        $newContent = Factory::create()->paragraph();

        //checking that an logged in user is present
        $response = $this
            ->put('/blog/' . $post->id, [
                'title' => $newTitle,
                'content' => $newContent,
                'user_id' => $user->id
            ]);
        $response->assertRedirect('/login');

        $response = $this
            ->actingAs($user)
            ->put('/blog/' . $post->id, [
                'title' => $newTitle,
                'content' => $newContent,
                'user_id' => $user->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertViewIs('blog.show')
            ->assertStatus(200);

        $post = Post::findOrFail($post->id);

        $this->assertSame($newTitle, $post->title);
        $this->assertSame($newContent, $post->content);
    }

    public function test_blog_post_can_be_deleted()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->delete('/blog/' . $post->id);

        $response->assertRedirect('/login');

        $this
            ->actingAs($user)
            ->delete('/blog/' . $post->id);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
