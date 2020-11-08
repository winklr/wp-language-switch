jQuery(document).ready(
    function ($) {
        $('#update-nav-menu')
            .click(
                function (e) {
                    if (e.target && e.target.className && -1 != e.target.className.indexOf('item-edit')) {
                        $('input[value=\'#wpls_switcher\'][type=text]').parent().parent().parent().each(
                            function () {
                                var item = $(this).attr('id').substring(19)
                                $(this).children('p:not( .field-move )').remove() // remove default fields we don't need

                                // item is a number part of id of parent menu item built by WordPress
                                // wpls_data is built server side with i18n strings without HTML and data retrieved from post meta
                                // the usage of attr method is safe before append call.
                                h = $('<input>').attr(
                                    {
                                        type: 'hidden',
                                        id: 'edit-menu-item-title-' + item,
                                        name: 'menu-item-title[' + item + ']',
                                        value: wpls_data.title
                                    }
                                )
                                $(this).append(h) // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append

                                h = $('<input>').attr(
                                    {
                                        type: 'hidden',
                                        id: 'edit-menu-item-url-' + item,
                                        name: 'menu-item-url[' + item + ']',
                                        value: '#wpls_switcher'
                                    }
                                )
                                $(this).append(h) // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append

                                // a hidden field which exits only if our jQuery code has been executed
                                h = $('<input>').attr(
                                    {
                                        type: 'hidden',
                                        id: 'edit-menu-item-wpls-detect-' + item,
                                        name: 'menu-item-wpls-detect[' + item + ']',
                                        value: 1
                                    }
                                )
                                $(this).append(h) // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append

                                const languagePair = [
                                    wpls_data.val[item] && wpls_data.val[item][`menu-item-${item}-language_0`] || '',
                                    wpls_data.val[item] && wpls_data.val[item][`menu-item-${item}-language_1`] || ''
                                ]

                                // keys of configured languages ('de', 'en', 'fr', ...)
                                const langKeys = Object.keys(wpls_data.languages)

                                // generate language select controls
                                const languageSelectControls = languagePair.map((language, index) => {
                                    const selectControl = $('<select>').attr('name', `menu-item-${item}-language_${index}`)

                                    // generate options
                                    const options = [
                                        $('<option>').attr('value', '')
                                            .prop('selected', !language)
                                            .text(wpls_data.strings['select_default']),
                                        ...langKeys.map((key) => $('<option>')
                                            .prop('selected', language === key)
                                            .attr('value', key).text(wpls_data.languages[key]))
                                    ]
                                    return selectControl.append(options)
                                })

                                $(this).prepend(languageSelectControls)
                            }
                        )
                    }
                }
            )
    }
)
