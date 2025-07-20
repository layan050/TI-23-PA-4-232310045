const OPENWEATHER_API_KEY = 'ea875d850449a1433b0eaf4825730c3f';

const GEOCODING_BASE_URL = 'http://api.openweathermap.org/geo/1.0/direct';
const WEATHER_BASE_URL = 'https://api.openweathermap.org/data/3.0/onecall';

export const getCoordinatesForCity = async (cityName) => {
  try {
    const response = await fetch(
      `${GEOCODING_BASE_URL}?q=${cityName}&limit=1&appid=${OPENWEATHER_API_KEY}`
    );
    const data = await response.json();

    if (data && data.length > 0) {
      return {
        lat: data[0].lat,
        lon: data[0].lon,
        name: data[0].name,
      };
    }
    return null;
  } catch (error) {
    console.error('Error fetching coordinates:', error);
    return null;
  }
};

export const getCitySuggestions = async (query) => {
  if (!query || query.length < 2) {
    return [];
  }
  try {
    const response = await fetch(

      `${GEOCODING_BASE_URL}?q=${query}&limit=10&appid=${OPENWEATHER_API_KEY}`
    );
    const data = await response.json();
    return data.map(item => ({
      name: item.name + (item.state ? `, ${item.state}` : '') + (item.country ? `, ${item.country}` : ''),
      lat: item.lat,
      lon: item.lon,
    }));
  } catch (error) {
    console.error('Error fetching city suggestions:', error);
    return [];
  }
};

export const getWeatherData = async (lat, lon) => {
  try {
    const response = await fetch(
      `${WEATHER_BASE_URL}?lat=${lat}&lon=${lon}&exclude=minutely,hourly,alerts&units=metric&appid=${OPENWEATHER_API_KEY}&lang=en`
    );
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching weather data:', error);
    throw error;
  }
};
