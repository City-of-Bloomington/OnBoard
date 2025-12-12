'use strict';

let ADDRESS_CHOOSER = {
    locator:  'https://bloomington.in.gov/arcgis-server/rest/services/Locators/CityCountyLocator12_2_24/GeocodeServer',
    results:  document.getElementById('results'),
    callback: {},

    start: function (callback) {
        ADDRESS_CHOOSER.callback = callback;

        document.getElementById('addressChooser').addEventListener('submit', (e)=>{
            const query   = document.getElementById('query').value;

            let   params  = new URLSearchParams({ text: query, f: 'json' }),
                  url     = new URL(`${ADDRESS_CHOOSER.locator}/suggest?${params}`);

            e.preventDefault();
            fetch(url)
                .then( (r) => {
                    if (!r.ok) { throw new Error(`HTTP error: ${r.status} `); }
                    return r.text();
                })
                .then( (t) => {
                    const j = JSON.parse(t);
                    if (j.suggestions) {
                        let html = '';
                        for (const s of j.suggestions) {
                            html += `<div><a onclick="ADDRESS_CHOOSER.findCandidates('${s.text}','${s.magicKey}')">${s.text}</a></div>`;
                        }
                        ADDRESS_CHOOSER.results.innerHTML = html;
                    }
                })
                .catch( (e) => { });
        });
    },

    findCandidates: (text, magicKey) => {
        let params = new URLSearchParams({ SingleLine:text, magicKey:magicKey, f:'json' }),
            url    = new URL(`${ADDRESS_CHOOSER.locator}/findAddressCandidates?${params}`);

        fetch(url)
            .then( (r) => {
                if (!r.ok) { throw new Error(`HTTP error: ${r.status} `); }
                return r.text();
            })
            .then( (t) => {
                const j = JSON.parse(t);
                if (j.candidates) {
                    ADDRESS_CHOOSER.results.innerHTML = '';
                    ADDRESS_CHOOSER.callback(j.candidates[0]);
                }
            })
            .catch( (e)=>{});
    }
};
