function HSDemo() {

  var settings = {
    headerMain: document.getElementById("headerMain").innerHTML,
    headerFluid: document.getElementById("headerFluid").innerHTML,
    headerDouble: document.getElementById("headerDouble").innerHTML,
    sidebarMain: document.getElementById("sidebarMain").innerHTML,
    sidebarCompact: document.getElementById("sidebarCompact").innerHTML
  }

  // Layouts
  var body = document.getElementsByTagName('body')[0],
    header = document.getElementsByClassName('navbar')[0],
    navbarVerticalAside = document.getElementsByClassName('navbar-vertical-aside')[0]; // Radios

  var radiosSkin = Array.prototype.slice.call(document.querySelectorAll('input[type=radio][name="layoutSkinsRadio"]'), 0),
    radiosSidebarMode = Array.prototype.slice.call(document.querySelectorAll('input[type=radio][name="sidebarLayoutOptions"]'), 0),
    radiosHeaderMode = Array.prototype.slice.call(document.querySelectorAll('input[type=radio][name="headerLayoutOptions"]'), 0); // Local Storage

  var skin = window.localStorage.getItem('hs-builder-skin') === null ? 'default' : window.localStorage.getItem('hs-builder-skin'),
    sidebarMode = window.localStorage.getItem('hs-builder-sidebar-mode') === null ? 'default' : window.localStorage.getItem('hs-builder-sidebar-mode'),
    headerMode = window.localStorage.getItem('hs-builder-header-mode') === null ? 'false' : window.localStorage.getItem('hs-builder-header-mode');

  var appendLayout = function appendLayout(str) {
    body.insertAdjacentHTML('afterbegin', str);
  };

  function addContainer() {
    var style = document.createElement('style');
    document.head.appendChild(style);
    style.textContent = "         \n      .content,\n      .footer {\n        width: 100%;\n        padding-right: 15px !important;\n        padding-left: 15px !important;\n        margin-right: auto;\n        margin-left: auto;\n      }\n      \n      @media (min-width: 1400px) {\n        .content,\n        .footer {\n          max-width: 1320px;\n        }\n      }       \n      \n      @media (min-width: 1400px) {\n        .content,\n        .footer {\n          max-width: 1320px;\n        }\n      }\n    ";
  }

  if (sidebarMode !== false || headerMode !== false) {
    body.classList.remove('navbar-vertical-aside-mini-mode');
  }

  if (headerMode == 'false') {
    if (!sidebarMode || sidebarMode === 'default') {
      appendLayout(settings.sidebarMain);
    } else if (sidebarMode === 'navbar-vertical-aside-compact-mode') {
      appendLayout(settings.sidebarCompact);
      document.body.className += ' navbar-vertical-aside-compact-mode navbar-vertical-aside-compact-mini-mode';
      var style = document.createElement('style');
      document.head.appendChild(style);
      style.textContent = "\n@media(min-width: 993px) {\n.js-navbar-vertical-aside-toggle-invoker {\ndisplay: none !important;\n}\n}\n";
    } else if (sidebarMode === 'navbar-vertical-aside-mini-mode') {
      appendLayout(settings.sidebarMain);
      document.body.className += ' navbar-vertical-aside-mini-mode';
    }

    document.body.className += ' footer-offset has-navbar-vertical-aside navbar-vertical-aside-show-xl';
  }

  if (headerMode === 'single') {
    if (skin === 'navbar-dark') {
      settings.headerFluid = settings.headerFluid.replace(/btn-ghost-secondary/g, 'btn-ghost-light');
    }

    appendLayout(settings.headerFluid);
    body.classList.add('footer-offset');
  } else if (headerMode === 'single-container') {
    if (skin === 'navbar-dark') {
      settings.headerFluid = settings.headerFluid.replace(/btn-ghost-secondary/g, 'btn-ghost-light');
    }

    appendLayout(settings.headerFluid);
    body.classList.add('footer-offset');
    var _header = document.getElementsByClassName('navbar')[0],
      oldHeaderContent = _header.innerHTML;
    _header.innerHTML = '<div class="container">' + oldHeaderContent + '</div>';
    addContainer();
  } else if (headerMode === 'double') {
    appendLayout(settings.headerDouble);
    body.classList.add('footer-offset');

    if ('scrollRestoration' in history) {
      // Back off, browser, I got this...
      history.scrollRestoration = 'manual';
    }
  } else if (headerMode === 'double-container') {
    appendLayout(settings.headerDouble);
    body.classList.add('footer-offset');
    var _header2 = document.getElementsByClassName('navbar')[0],
      fisrtElement = _header2.firstElementChild;
    fisrtElement.innerHTML = '<div class="navbar-dark w-100"> <div class="container">' + fisrtElement.firstElementChild.innerHTML + '</div> </div>';
    _header2.innerHTML = fisrtElement.innerHTML + ' <div class="container">' + _header2.lastElementChild.innerHTML + '</div>';
    addContainer();

    if ('scrollRestoration' in history) {
      // Back off, browser, I got this...
      history.scrollRestoration = 'manual';
    }
  } else {
    appendLayout(settings.headerMain);
  }

  if (skin && headerMode !== 'double' && headerMode !== 'double-container') {
    var _header3 = document.getElementsByClassName('navbar')[0],
      sidebar = document.getElementsByClassName('navbar-vertical-aside')[0];

    if (headerMode === 'single' || headerMode === 'single-container') {
      _header3.classList.add(skin);
    }

    if (sidebar) {
      sidebar.classList.add(skin);
    }

    if (skin === 'navbar-light') {
      if (_header3) {
        _header3.classList.remove('navbar-bordered');
      }

      if (sidebar) {
        sidebar.classList.remove('navbar-bordered');
      }
    } else if (skin === 'navbar-dark') {
      if (sidebar) {
        for (var i = 0; i < document.querySelectorAll('aside .navbar-brand-logo').length; i++) {
          document.querySelectorAll('aside .navbar-brand-logo')[i].setAttribute('src', document.querySelectorAll('aside .navbar-brand-logo')[0].getAttribute('src').replace('logo.svg', 'logo-white.svg'));
        }
      } else {
        for (var i = 0; i < document.querySelectorAll('header .navbar-brand-logo').length; i++) {
          document.querySelectorAll('header .navbar-brand-logo')[i].setAttribute('src', document.querySelectorAll('header .navbar-brand-logo')[0].getAttribute('src').replace('logo.svg', 'logo-white.svg'));
        }
      }

      for (var i = 0; i < document.getElementsByClassName('navbar-brand-logo-mini').length; i++) {
        document.getElementsByClassName('navbar-brand-logo-mini')[i].setAttribute('src', document.getElementsByClassName('navbar-brand-logo-mini')[0].getAttribute('src').replace('logo-short.svg', 'logo-short-white.svg'));
      }

      for (var i = 0; i < document.getElementsByClassName('navbar-brand-logo-short').length; i++) {
        document.getElementsByClassName('navbar-brand-logo-short')[i].setAttribute('src', document.getElementsByClassName('navbar-brand-logo-short')[0].getAttribute('src').replace('logo-short.svg', 'logo-short-white.svg'));
      }
    }
  }

  radiosSkin.forEach(function (radio) {
    if (skin === radio.value) {
      radio.checked = true;
    }

    radio.addEventListener('change', function () {
      skin = radio.value;
    });
  });
  radiosSidebarMode.forEach(function (radio) {
    if (sidebarMode === radio.value) {
      radio.checked = true;
    }

    radio.addEventListener('change', function () {
      sidebarMode = radio.value;
      radiosSkin.forEach(function (radio) {
        if (skin === radio.value) {
          radio.checked = true;
        }

        radio.disabled = false;
      });
      radiosHeaderMode.forEach(function (radio) {
        radio.checked = false;
        headerMode = false;
      });
    });
  });
  radiosHeaderMode.forEach(function (radio) {
    if (headerMode === radio.value) {
      radio.checked = true;

      if (radio.value === 'double' || radio.value === 'double-container') {
        radiosSkin.forEach(function (radio) {
          radio.checked = false;
          radio.disabled = true;
        });
        document.getElementById('js-builder-disabled').style.opacity = 1;
      }

      radiosSidebarMode.forEach(function (radio) {
        radio.checked = false;
      });
    }

    radio.addEventListener('change', function (e) {
      if (radio.value !== 'default') {
        headerMode = radio.value;
      } else {
        headerMode = false;
      }

      if (e.target.value === 'double' || radio.value === 'double-container') {
        radiosSkin.forEach(function (radio) {
          radio.checked = false;
          radio.disabled = true;
        });
      } else {
        radiosSkin.forEach(function (radio) {
          if (skin === false && radio.value === 'default' || skin === radio.value) {
            radio.checked = true;
          }

          radio.disabled = false;
        });
      }

      radiosSidebarMode.forEach(function (radio) {
        radio.checked = false;
        sidebarMode = false;
      });
    });
  });
  Array.prototype.slice.call(document.querySelectorAll('.custom-checkbox-card-input'), 0).forEach(function (radio) {
    radio.addEventListener('change', function () {
      radiosSkin.forEach(function (radio) {
        if (radio.disabled) {
          document.getElementById('js-builder-disabled').style.opacity = 1;
        } else {
          document.getElementById('js-builder-disabled').style.opacity = 0;
        }
      });
    });
  });
  document.getElementById('js-builder-preview').addEventListener('click', function () {
    location.reload();

    if (skin) {
      window.localStorage.setItem('hs-builder-skin', skin);
    }

    if (sidebarMode) {
      window.localStorage.setItem('hs-builder-sidebar-mode', sidebarMode);
    }

    window.localStorage.setItem('hs-builder-header-mode', headerMode);
  });
  document.getElementById('js-builder-reset').addEventListener('click', function () {
    window.localStorage.removeItem('hs-builder-skin');
    window.localStorage.removeItem('hs-builder-sidebar-mode');
    window.localStorage.removeItem('hs-builder-header-mode');
    location.reload();
  });

  document.getElementById("headerMain").parentNode.removeChild(document.getElementById("headerMain"));
  document.getElementById("headerFluid").parentNode.removeChild(document.getElementById("headerFluid"));
  document.getElementById("headerDouble").parentNode.removeChild(document.getElementById("headerDouble"));
  document.getElementById("sidebarMain").parentNode.removeChild(document.getElementById("sidebarMain"));
  document.getElementById("sidebarCompact").parentNode.removeChild(document.getElementById("sidebarCompact"));
}

HSDemo();

function submitByAjax(formSelector, options = {}) {
    $(document).on('submit', formSelector, function (e) {
        e.preventDefault();

        if (options.hasEditors) {
            if (options.languages && Array.isArray(options.languages)) {
                options.languages.forEach(lang => {
                    let editor = document.querySelector(`#${lang}_editor`);
                    $(`#${lang}_hiddenArea`).val(editor.children[0].innerHTML);
                });
            } else {
                let editor = document.querySelector('#editor');
                $("#hiddenArea").val(editor.children[0].innerHTML);
            }
        }

        let formData = new FormData(this);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.post({
            url: $(this).attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                $('.error-text').text('');
                $('input, textarea, select, .select2-selection, .image-preview, .bootstrap-tagsinput').removeClass('is-invalid');

                if (data?.errors) {
                    let firstErrorElement = null;
                    $.each(data.errors, function (field, messages) {
                        if (messages.code.startsWith('images')) {
                            let allMessages = Array.isArray(messages.message) ? messages.message.join('<br>') : messages.message;
                            $('[data-error="images"]').html(allMessages);
                            $('#coba .image-preview').addClass('is-invalid');
                        }


                        else if (messages.code.startsWith('attribute_id')) {
                            let allMessages = Array.isArray(messages.message) ? messages.message.join('<br>') : messages.message;
                            $('[data-error="attribute_id"]').html(allMessages);

                            let select = $('[name="attribute_id[]"]');
                            select.addClass('is-invalid');
                            if (select.hasClass('js-select2-custom')) {
                                select.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            }
                            return;
                        }

                        else if (messages.code.startsWith('choice_options_')) {
                            let allMessages = Array.isArray(messages.message) ? messages.message.join('<br>') : messages.message;

                            let input = $('[name="' + messages.code + '[]"]');
                            input.addClass('is-invalid');

                            let container = input.closest('div').find('.bootstrap-tagsinput');
                            container.addClass('is-invalid');

                            if (!$('[data-error="' + messages.code + '"]').length) {
                                container.after('<span class="error-text" data-error="' + messages.code + '"></span>');
                            }
                            $('[data-error="' + messages.code + '"]').html(allMessages);

                            return;
                        }

                        $(`.error-text[data-error="${messages.code}"]`).text(messages.message);
                        let input = $(`[name="${messages.code}"]`);
                        if (!input.length && messages.code.includes('.')) {
                            let base = messages.code.split('.')[0];
                            input = $(`[name="${base}[]"]`).eq(messages.code.split('.')[1]);
                        }
                        if (input.length) {
                            input.addClass('is-invalid');
                            if (input.hasClass('js-select2-custom')) {
                                input.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            }
                        }

                        if (!firstErrorElement) {
                            let input = $(`[name="${messages.code}"]`);
                            if (!input.length && messages.code.includes('.')) {
                                let base = messages.code.split('.')[0];
                                input = $(`[name="${base}[]"]`).eq(messages.code.split('.')[1]);
                            }
                            if (input.length) {
                                firstErrorElement = input;
                            } else {
                                firstErrorElement = $(`.error-text[data-error="${messages.code}"]`);
                            }
                        }
                    });
                    if (firstErrorElement && firstErrorElement.length) {
                        $('html, body').animate({
                            scrollTop: firstErrorElement.offset().top - 100
                        }, 500);
                    }
                } else {
                    toastr.success(options.successMessage ?? data.success_message, {
                        CloseButton: true,
                        ProgressBar: true
                    });

                    if (options.redirectUrl) {
                        setTimeout(function () {
                            location.href = options.redirectUrl;
                        }, options.redirectDelay ?? 2000);
                    }
                }
            },
            error: function (xhr) {
                console.log(xhr)
            }
        });
    });
}




