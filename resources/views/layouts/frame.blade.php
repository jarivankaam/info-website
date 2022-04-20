<x-partials.header>
</x-partials.header>

<iframe class="website" src="@yield('path_url')" frameborder="0"></iframe>

<div class="frame">
</div>

<div class="developer">
    <span class="introduction">@yield('intro_text')</span>
    <h2 class="student">
        <span>@yield('student_name')</span>
        <small class="title_text">
            @yield('title_text')
            <strong>software developer</strong>
        </small>
    </h2>
    <div class="i18n">
        <div class="flags">
            <a href="#">
                <svg xmlns="http://www.w3.org/2000/svg" id="flag-icons-nl" viewBox="0 0 640 480">
                    <path fill="#21468b" d="M0 0h640v480H0z"/>
                    <path fill="#fff" d="M0 0h640v320H0z"/>
                    <path fill="#ae1c28" d="M0 0h640v160H0z"/>
                </svg>
            </a>
            <a href="#">
                <svg xmlns="http://www.w3.org/2000/svg" id="flag-icons-gb" viewBox="0 0 640 480">
                    <path fill="#012169" d="M0 0h640v480H0z"/>
                    <path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z"/>
                    <path fill="#C8102E" d="m424 281 216 159v40L369 281h55zm-184 20 6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z"/>
                    <path fill="#FFF" d="M241 0v480h160V0H241zM0 160v160h640V160H0z"/>
                    <path fill="#C8102E" d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z"/>
                </svg>
            </a>
        </div>
    </div>
</div>
<div class="branding">
    <a href="https://www.curio.nl/mbo/techniek-en-technologie/softwaredeveloper/" class="logo" target="_blank">
        <h1>
            <svg width="66" height="23" viewBox="0 0 66 23"><path d="M7.935 22.769C3.414 22.769 0 19.407 0 14.905 0 10.402 3.414 7.01 7.935 7.01c2.952 0 5.444 1.48 6.643 3.732l-3.66 2.097c-.554-1.018-1.66-1.666-2.983-1.666-2.153 0-3.69 1.604-3.69 3.732 0 2.097 1.537 3.7 3.69 3.7 1.476 0 2.676-.709 3.26-1.912l3.844 1.727c-1.138 2.621-3.875 4.349-7.104 4.349m22.384-7.375c0 4.303-2.956 7.315-7.206 7.315-4.219 0-7.175-3.012-7.175-7.315V7.31h4.311v8.084c0 1.936 1.109 3.166 2.864 3.166s2.864-1.23 2.864-3.166V7.31h4.342v8.084zm11.504-3.963h-4.968v10.978h-4.32V7.31h9.288zm1.869 10.978h4.322V7.316h-4.322v15.093zm4.782-19.796c0 1.445-1.196 2.643-2.636 2.643-1.441 0-2.637-1.198-2.637-2.643C43.201 1.199 44.397 0 45.838 0c1.44 0 2.636 1.199 2.636 2.613zm5.806 12.292c0 2.097 1.539 3.7 3.601 3.7 2.032 0 3.602-1.603 3.602-3.7 0-2.159-1.57-3.732-3.602-3.732-2.062 0-3.601 1.573-3.601 3.732m11.45 0c0 4.502-3.416 7.864-7.849 7.864-4.463 0-7.85-3.362-7.85-7.864 0-4.503 3.387-7.895 7.85-7.895 4.433 0 7.85 3.392 7.85 7.895" fill-rule="evenodd"></path></svg>
        </h1>
    </a>
</div>

<x-partials.footer />
