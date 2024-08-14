document.addEventListener('DOMContentLoaded', function() {
    const cityInput = document.getElementById('cityInput');
    const getWeatherBtn = document.getElementById('getWeatherBtn');
    const weatherInfo = document.getElementById('weatherInfo');
    const forecastInfo = document.getElementById('forecastInfo');
    const recentSearchesList = document.getElementById('recentSearchesList');

    getWeatherBtn.addEventListener('click', function() {
        const cityName = cityInput.value.trim();
        if (cityName !== '') {
            getWeather(cityName);
        }
    });

    async function getWeather(cityName) {
        const apiUrl = `weather.php?city=${cityName}`;
        try {
            const response = await fetch(apiUrl);
            const data = await response.json();
            if (data.error) {
                weatherInfo.innerHTML = `<p>${data.error}</p>`;
                forecastInfo.innerHTML = '';
            } else {
                displayWeather(data);
                saveRecentSearch(cityName);
                updateRecentSearches();
            }
        } catch (error) {
            weatherInfo.innerHTML = '<p>There was an error fetching the weather data. Please try again later.</p>';
            forecastInfo.innerHTML = '';
        }
    }

    function displayWeather(data) {
        weatherInfo.innerHTML = `
            <h2>${data.city}</h2>
        `;

        forecastInfo.innerHTML = data.forecast.map(day => `
            <div class="forecast-day">
                <h3>${day.date}</h3>
                <img src="http://openweathermap.org/img/wn/${day.icon}.png" alt="${day.weather}">
                <p>Temp: ${day.temperature} °C</p>
                <p>Humidity: ${day.humidity}%</p>
                <p>${day.weather}</p>
            </div>
        `).join('');
    }

    function saveRecentSearch(city) {
        let recentSearches = JSON.parse(localStorage.getItem('recentSearches')) || [];
        if (!recentSearches.includes(city)) {
            recentSearches.push(city);
            if (recentSearches.length > 5) {
                recentSearches.shift();  // Keep only the last 5 searches
            }
            localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
        }
    }

    function updateRecentSearches() {
        let recentSearches = JSON.parse(localStorage.getItem('recentSearches')) || [];
        recentSearchesList.innerHTML = '';
        recentSearches.forEach(city => {
            let li = document.createElement('li');
            li.textContent = city;
            li.addEventListener('click', function() {
                getWeather(city);
            });
            recentSearchesList.appendChild(li);
        });
    }

    // Load recent searches on page load
    updateRecentSearches();
});
