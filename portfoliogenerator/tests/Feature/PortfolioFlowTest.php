<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

class PortfolioFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_https_proxy_headers_are_used_for_asset_urls(): void
    {
        $this->actingAs(User::factory()->create());

        $this->withServerVariables(['HTTP_X_FORWARDED_PROTO' => 'https'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('https://localhost/css/portfolio.css', false)
            ->assertSee('https://localhost/js/portfolio.js', false);
    }

    public function test_visitors_can_create_accounts_sign_in_and_manage_their_portfolios(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create account');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Welcome!')
            ->assertSee('auth-back')
            ->assertDontSee('class="nav-account" href="'.route('login').'"', false)
            ->assertDontSee('nav-register')
            ->assertSee('Create an account')
            ->assertDontSee('footer-back');

        $this->post(route('register.store'), [
            'name' => 'Morgan Chen',
            'email' => 'MORGAN@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('account.index'));

        $user = User::where('email', 'morgan@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame('secure-password', $user->password);

        $response = $this->post(route('portfolios.store'), [
            'full_name' => 'Morgan Chen',
            'email' => 'morgan@example.com',
        ]);
        $portfolio = $user->portfolios()->firstOrFail();

        $response->assertRedirect(route('portfolios.templates', $portfolio->management_token));
        $this->get(route('account.index'))
            ->assertOk()
            ->assertSee('Morgan Chen')
            ->assertSee('class="back-link" href="'.route('home').'"', false)
            ->assertSee('Profile')
            ->assertSee('Account settings')
            ->assertSee('How it works')
            ->assertSee('Sign out')
            ->assertSee(route('portfolios.manage', $portfolio->management_token), false);

        $this->get(route('account.profile'))->assertOk()->assertSee('Your profile.');
        $this->get(route('account.settings'))->assertOk()->assertSee('Account settings.');
        $this->put(route('account.profile.update'), [
            'name' => 'Morgan C. Chen',
            'email' => 'morgan@example.com',
        ])->assertRedirect(route('account.profile'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Morgan C. Chen']);
        $this->put(route('account.password.update'), [
            'current_password' => 'secure-password',
            'password' => 'another-secure-password',
            'password_confirmation' => 'another-secure-password',
        ])->assertRedirect(route('account.settings'));
        $this->assertTrue(password_verify('another-secure-password', $user->fresh()->password));

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->post(route('login.store'), [
            'email' => 'morgan@example.com',
            'password' => 'another-secure-password',
        ])->assertRedirect(route('account.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_visitors_are_sent_to_sign_in_before_the_home_page(): void
    {
        $this->get(route('home'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee('footer-back');
    }

    public function test_expired_logout_requests_redirect_to_sign_in(): void
    {
        $request = Request::create('/sign-out', 'POST');
        $response = app(\Illuminate\Contracts\Debug\ExceptionHandler::class)
            ->render($request, new TokenMismatchException);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('login'), $response->headers->get('Location'));
    }

    public function test_guests_must_sign_in_before_creating_or_managing_portfolios(): void
    {
        $portfolio = Portfolio::create([
            'uuid' => 'ba6ad5aa-48a2-45f9-9cf0-d650bd593777',
            'management_token' => str_repeat('m', 64),
            'full_name' => 'Morgan Chen',
            'email' => 'morgan@example.com',
        ]);

        $this->get(route('portfolios.create'))->assertRedirect(route('login'));
        $this->post(route('portfolios.store'), [])->assertRedirect(route('login'));
        $this->get(route('portfolios.templates', $portfolio->management_token))->assertRedirect(route('login'));
        $this->patch(route('portfolios.template', $portfolio->management_token), ['template' => 'modern'])->assertRedirect(route('login'));
        $this->get(route('portfolios.manage', $portfolio->management_token))->assertRedirect(route('login'));
        $this->get(route('portfolios.edit', $portfolio->management_token))->assertRedirect(route('login'));
        $this->put(route('portfolios.update', $portfolio->management_token), [])->assertRedirect(route('login'));
        $this->delete(route('portfolios.destroy', $portfolio->management_token))->assertRedirect(route('login'));

        $this->get(route('portfolios.show', $portfolio))
            ->assertOk()
            ->assertSee('Morgan Chen')
            ->assertDontSee('Create portfolio');
        $this->assertDatabaseCount('portfolios', 1);
    }

    public function test_new_users_return_to_portfolio_creation_after_registering(): void
    {
        $this->get(route('portfolios.create'))->assertRedirect(route('login'));
        $this->get(route('register'))->assertOk();

        $this->post(route('register.store'), [
            'name' => 'Morgan Chen',
            'email' => 'morgan@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('portfolios.create'));

        $this->assertAuthenticated();
        $this->get(route('portfolios.create'))
            ->assertOk()
            ->assertSee('portfolio-cta is-active', false)
            ->assertSee('aria-current="page"', false)
            ->assertDontSee('footer-back');
    }

    public function test_a_portfolio_can_be_created_styled_viewed_edited_and_deleted(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('portfolios.store'), [
            'full_name' => 'Morgan Chen',
            'profile_image_url' => 'https://images.example.com/morgan.jpg',
            'email' => 'morgan@example.com',
            'contact_number' => '+1 555 0100',
            'address' => 'Toronto, Canada',
            'about_me' => 'I make useful things with thoughtful teams.',
            'education' => [[
                'qualification' => 'BDes',
                'institution' => 'North College',
                'year' => '2022–2026',
                'details' => 'Communication design',
            ]],
            'skills' => ['Research', 'Prototyping'],
            'projects' => [[
                'name' => 'Field Notes',
                'description' => 'A community resource directory.',
                'url' => 'https://example.com/field-notes',
            ]],
            'experience' => [[
                'role' => 'Design intern',
                'company' => 'Common Ground',
                'period' => '2025',
                'details' => 'Worked across research and product.',
            ]],
            'links' => [[
                'label' => 'LinkedIn',
                'url' => 'https://linkedin.com/in/morgan',
            ]],
        ]);

        $portfolio = Portfolio::firstOrFail();
        $response->assertRedirect(route('portfolios.templates', $portfolio->management_token));
        $this->assertDatabaseHas('portfolios', [
            'uuid' => $portfolio->uuid,
            'profile_image' => 'https://images.example.com/morgan.jpg',
            'full_name' => 'Morgan Chen',
            'email' => 'morgan@example.com',
        ]);
        $this->assertSame(['Research', 'Prototyping'], $portfolio->skills);

        $this->get(route('portfolios.templates', $portfolio->management_token))
            ->assertOk()
            ->assertSee('portfolio-cta is-active', false)
            ->assertSee('Back to manage')
            ->assertSee(route('portfolios.manage', $portfolio->management_token), false)
            ->assertSee('Simple')
            ->assertSee('Modern')
            ->assertSee('Creative')
            ->assertSee('Preview portfolio');

        $this->patch(route('portfolios.template', $portfolio->management_token), ['template' => 'creative'])
            ->assertRedirect(route('portfolios.show', $portfolio));

        $this->get(route('portfolios.show', $portfolio))
            ->assertOk()
            ->assertSee('creative-portfolio')
            ->assertSee('Field Notes')
            ->assertSee('Communication design')
            ->assertSee('Morgan Chen')
            ->assertSee('PORTFOLIO PREVIEW')
            ->assertSee('Create portfolio')
            ->assertDontSee('Create your own')
            ->assertDontSee($portfolio->management_token)
            ->assertDontSee('Edit details');

        $this->get(route('portfolios.manage', $portfolio->uuid))->assertNotFound();
        $this->get(route('portfolios.manage', $portfolio->management_token))
            ->assertOk()
            ->assertSee('class="back-link" href="'.route('account.index').'"', false)
            ->assertSee('My portfolios')
            ->assertSee('Delete portfolio');
        $this->get(route('portfolios.edit', $portfolio->management_token))->assertOk()->assertSee('Morgan Chen');

        $this->put(route('portfolios.update', $portfolio->management_token), [
            'full_name' => 'Morgan Chen-Smith',
            'email' => 'morgan@example.com',
            'about_me' => 'Updated introduction.',
            'skills' => ['Research'],
        ])->assertRedirect(route('portfolios.manage', $portfolio->management_token));

        $this->assertDatabaseHas('portfolios', [
            'uuid' => $portfolio->uuid,
            'full_name' => 'Morgan Chen-Smith',
            'template' => 'creative',
        ]);

        $this->delete(route('portfolios.destroy', $portfolio->management_token))->assertRedirect(route('home'));
        $this->assertDatabaseMissing('portfolios', ['uuid' => $portfolio->uuid]);
    }

    public function test_only_the_three_supported_templates_can_be_selected(): void
    {
        $this->actingAs(User::factory()->create());

        $portfolio = Portfolio::create([
            'uuid' => '92d0d5aa-48a2-45f9-9cf0-d650bd593777',
            'management_token' => str_repeat('x', 64),
            'full_name' => 'Taylor Rivera',
            'email' => 'taylor@example.com',
        ]);

        $this->from(route('portfolios.templates', $portfolio->management_token))
            ->patch(route('portfolios.template', $portfolio->management_token), ['template' => 'unexpected'])
            ->assertSessionHasErrors('template');

        $this->assertSame('simple', $portfolio->fresh()->template);
    }

    public function test_profile_and_external_links_reject_non_http_schemes(): void
    {
        $this->actingAs(User::factory()->create());

        $this->from(route('portfolios.create'))
            ->post(route('portfolios.store'), [
                'full_name' => 'Casey Park',
                'email' => 'casey@example.com',
                'profile_image_url' => 'javascript://example.com/photo',
                'projects' => [['url' => 'javascript://example.com/project']],
                'links' => [['url' => 'javascript://example.com/profile']],
            ])
            ->assertSessionHasErrors([
                'profile_image_url',
                'projects.0.url',
                'links.0.url',
            ]);

        $this->assertDatabaseCount('portfolios', 0);
    }
}
