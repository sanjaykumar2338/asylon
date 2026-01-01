@extends('marketing.layout')

@section('title', 'Asylon | Home')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="hero-section">
    <div class="site-container">
        <div class="hero-grid">
            <div class="hero-content">
                <div class="hero-title">
                    <h2>Give Them a Safe Way to Speak Up. <span> Get a Clear Way to Respond </span></h2>
                </div>
                <p>
                    Asylon is a secure reporting and threat-assessment platform built by a veteran law enforcement
                    officer for schools, churches, and organizations. It gives students, staff, and members a safe
                    way to share concerns and gives your team a clear workflow to
                    review, respond, and document every case.
                </p>

                <div class="hero-actions">
                    <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Schedule a 20-Minute Call</a>
                    <a href="{{ route('marketing.resources') }}" class="site-btn-light">Watch 2-Minute Overview</a>
                </div>
            </div>

            <div class="hero-image">
                <img src="{{ $assetBase }}/images/h1.png" alt="Dashboard Image" class="pc-image">
                <img src="{{ $assetBase }}/images/Dashboard-as-1.png" alt="Dashboard Image" class="mobile-image">

            </div>
        </div>
    </div>
    </section>

    <section class="who-we-serve">
        <div class="site-container">

            <div class="section-header text-center">
                <div class="section-subtitle">
                    <span>WHO WE SERVE</span>
                </div>
                <div class="section-title">
                    <h2>Built for Real-World Safety Teams</h2>
                </div>
            </div>
            <div class="serve-grid">
                <div class="serve-card">
                    <div class="card-icon">
                        <img src="{{ $assetBase }}/images/Group.png" alt="K-12 Schools & Districts">
                    </div>
                    <h3>K-12 Schools & Districts</h3>
                    <p>
                        Give students and staff a confidential way to report bullying, threats, weapons talk, and
                        self-harm and route every concern through a clear, documented threat-assessment process.
                    </p>
                    <a href="{{ route('marketing.solutions.schools') }}" class="site-btn-light">Explore Asylon for Schools</a>
                </div>

                <div class="serve-card">
                    <div class="card-icon">
                        <img src="{{ $assetBase }}/images/Vector.png" alt="Churches & Ministries">
                    </div>
                    <h3>Churches & Ministries</h3>
                    <p>
                        Help members and volunteers share concerns about safety, domestic spillover, and mental health
                        crises while your safety and pastoral teams see every report in one place.
                    </p>
                    <a href="{{ route('marketing.solutions.churches') }}" class="site-btn-light">Explore Asylon for Churches</a>
                </div>

                <div class="serve-card">
                    <div class="card-icon">
                        <img src="{{ $assetBase }}/images/Vector2.png" alt="Workplaces & Organizations">
                    </div>
                    <h3>Workplaces & Organizations</h3>
                    <p>
                        Create a trusted channel for reporting workplace violence, harassment, and policy violations,
                        and connect every report to HR, legal, and security workflows.
                    </p>
                    <a href="{{ route('marketing.solutions.organizations') }}" class="site-btn-light">Explore Asylon for Organizations</a>
                </div>
            </div>

        </div>
    </section>

    <section class="how-it-works">
        <div class="site-container">

            <div class="section-header text-center">
                <div class="section-subtitle">
                    <span>HOW IT WORKS</span>
                </div>
                <div class="section-title">
                    <h2>From Anonymous Tip to Documented <br> Action Plan</h2>
                </div>
            </div>
            <div class="steps-wrapper">

                <div class="step">
                    <div class="step-number">01</div>
                    <div class="step-content">
                        <h3>A Concerned Person Submits a Report</h3>
                        <p>Students, staff, members, or employees submit a secure report from any device anonymously or
                            by name.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">02</div>
                    <div class="step-content">
                        <h3>Your Multidisciplinary Team Is Notified</h3>
                        <p>Asylon routes the report to the right leaders and notifies your safety, counseling, or HR
                            team.
                        </p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">03</div>
                    <div class="step-content">
                        <h3>You Investigate, Document, and Act</h3>
                        <p>Assign reviewers, add notes and tasks, and log every step you take all in one case record.
                        </p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">04</div>
                    <div class="step-content">
                        <h3>You Track Outcomes and Patterns Over Time</h3>
                        <p>Review trends by category, campus, and timeframe so you can spot patterns and strengthen your
                            prevention plans.</p>
                    </div>
                </div>

            </div>
            <div class="action-btn text-center">
                <a href="{{ route('marketing.how') }}" class="site-btn-dark">See the Full Workflow</a>
            </div>

        </div>
    </section>

    <section class="why-asylon">
        <div class="site-container">

            <div class="asylon-grid">
                <div class="column">

                    <div class="section-header ">
                        <div class="section-subtitle">
                            <span>WHY ASYLON</span>
                        </div>
                        <div class="section-title">
                            <h2>Built for Prevention, Not Just Paperwork</h2>
                        </div>
                    </div>

                    <ul class="site-list">
                        <li> Created by a veteran law enforcement and school safety practitioner.</li>
                        <li>Designed for real threat-assessment teams not a generic HR hotline.</li>
                        <li>Supports anonymous reports with two-way follow-up, when enabled.</li>
                        <li>Clear roles and permissions for admins, leaders, and reviewers.</li>
                        <li>Documentation that stands up to board reviews, audits, and law enforcement needs.</li>
                    </ul>

                </div>
                <div class="column">
                    <img src="{{ $assetBase }}/images/Rectangle 3463506.png" alt="">
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-block">
        <div class="site-container">

            <div class="section-header text-center">
                <div class="section-subtitle">
                    <span>Testimonials & Trust</span>
                </div>
                <div class="section-title">
                    <h2>Trusted by Leaders Who Carry the <br> Weight of Safety</h2>
                </div>
            </div>

            <div class="testimonials-slider">
                <div class="testimonial-card">
                    <p>Asylon helps our team catch concerns earlier and document every step we take to support our
                        students.
                    </p>
                    <div class="testimonial-author">
                        <div class="main-bx">

                            <img src="{{ $assetBase }}/images/author.png.png" alt="Sara Mohamed">
                            <div class="author-info">
                                <strong>Sara Mohamed</strong>
                                <span>Jakatar</span>
                            </div>
                        </div>
                        <div class="author-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i></div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p>Asylon helps our team catch concerns earlier and document every step we take to support our
                        students.
                    </p>
                    <div class="testimonial-author">

                        <div class="main-bx">


                            <img src="{{ $assetBase }}/images/1author.png.png" alt="Sara Mohamed">
                            <div class="author-info">
                                <strong>Sara Mohamed</strong>
                                <span>Jakatar</span>
                            </div>
                        </div>
                        <div class="author-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i></div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p>Asylon helps our team catch concerns earlier and document every step we take to support our
                        students.
                    </p>
                    <div class="testimonial-author">

                        <div class="main-bx">

                            <img src="{{ $assetBase }}/images/2author.png.png" alt="Sara Mohamed">
                            <div class="author-info">
                                <strong>Sara Mohamed</strong>
                                <span>Jakatar</span>
                            </div>
                        </div>
                        <div class="author-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i></div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p>Asylon helps our team catch concerns earlier and document every step we take to support our
                        students.
                    </p>
                    <div class="testimonial-author">


                        <div class="main-bx">
                            <img src="{{ $assetBase }}/images/1author.png.png" alt="Sara Mohamed">

                            <div class="author-info">
                                <strong>Sara Mohamed</strong>
                                <span>Jakatar</span>
                            </div>
                        </div>

                        <div class="author-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i></div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="partners-section">
        <div class="site-container2 ">
            <div class="section-header text-center">
                <div class="section-title">
                    <h2>Our partners</h2>
                </div>
            </div>

            <div class="marqu-slider">
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image-03.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image-6.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image7.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image7.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image-02.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image-02.png" alt=""></div>

                <div class="logo-bx"><img src="{{ $assetBase }}/images/image-03.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image-6.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image7.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image7.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image-02.png" alt=""></div>
                <div class="logo-bx"><img src="{{ $assetBase }}/images/image-02.png" alt=""></div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="site-container">
            <img src="{{ $assetBase }}/images/Vectore.png" alt="Bird Icon" class="cta-icon">
            <div class="section-title">
                <h2>Ready to see how Asylon would fit <span> your safety plan? </span></h2>
            </div>
            <div class="action-btn text-center">
                <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Schedule a Consultation</a>
            </div>
        </div>
    </section>
@endsection
