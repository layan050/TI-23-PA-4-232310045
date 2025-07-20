import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, ScrollView, TouchableOpacity, StyleSheet, ActivityIndicator } from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import WeatherCard from '../components/WeatherCard';
import { getWeatherData } from '../api/weatherApi';
import moment from 'moment';

const FavoritesScreen = ({ favoriteLocations, onRemoveFavorite, onSelectFavorite }) => { // 'onSearchSubmit' removed
  const [favoriteWeatherData, setFavoriteWeatherData] = useState({});
  const [isLoadingFavorites, setIsLoadingFavorites] = useState(true);

  const fetchWeatherDataForFavorites = useCallback(async () => {
    setIsLoadingFavorites(true);
    const dataPromises = favoriteLocations.map(async (loc) => {
      try {
        const weather = await getWeatherData(loc.lat, loc.lon);
        return { [loc.name]: weather };
      } catch (error) {
        console.error(`Error fetching weather for ${loc.name}:`, error);
        return { [loc.name]: null };
      }
    });

    const results = await Promise.all(dataPromises);
    const compiledData = results.reduce((acc, current) => ({ ...acc, ...current }), {});
    setFavoriteWeatherData(compiledData);
    setIsLoadingFavorites(false);
  }, [favoriteLocations]);

  useEffect(() => {
    if (favoriteLocations.length > 0) {
      fetchWeatherDataForFavorites();
    } else {
      setIsLoadingFavorites(false);
      setFavoriteWeatherData({});
    }
  }, [favoriteLocations, fetchWeatherDataForFavorites]);

  const getDayName = (timestamp) => {
    return moment.unix(timestamp).format('dddd');
  };

  const getFormattedDate = (timestamp) => {
    return moment.unix(timestamp).format('MMM D');
  };

  return (
    <View style={favoritesScreenStyles.fullScreenContainer}>
      <ScrollView contentContainerStyle={favoritesScreenStyles.scrollViewContent} style={favoritesScreenStyles.scrollView}>
        <Text style={favoritesScreenStyles.favoritesHeader}>Your Favorite Locations</Text>

        {isLoadingFavorites ? (
          <View style={favoritesScreenStyles.loadingContainer}>
            <ActivityIndicator size="large" color="#4285F4" />
            <Text style={favoritesScreenStyles.loadingText}>Loading favorite data...</Text>
          </View>
        ) : favoriteLocations.length > 0 ? (
          <View style={favoritesScreenStyles.favoritesList}>
            {favoriteLocations.map((item, index) => {
              const weather = favoriteWeatherData[item.name];
              const currentDayWeather = weather?.current;

              return (
                <View key={index} style={favoritesScreenStyles.favoriteItemContainer}>
                  <View style={favoritesScreenStyles.favoriteItemHeader}>
                    <Text style={favoritesScreenStyles.favoriteLocationName}>{item.name}</Text>
                    <TouchableOpacity onPress={() => onRemoveFavorite(item)} style={favoritesScreenStyles.removeButton}>
                      <Icon name="close-circle" size={24} color="#FF5C5C" />
                    </TouchableOpacity>
                  </View>
                  <TouchableOpacity onPress={() => onSelectFavorite(item)}>
                    {currentDayWeather ? (
                      <WeatherCard
                        hideDayAndDate={true}
                        isFavoriteCard={true}
                        humidity={currentDayWeather.humidity}
                        tempHigh={Math.round(currentDayWeather.temp || currentDayWeather.feels_like)}
                        tempLow={Math.round(currentDayWeather.feels_like || currentDayWeather.temp)}
                        condition={currentDayWeather.weather[0].description}
                        iconCode={currentDayWeather.weather[0].icon}
                      />
                    ) : (
                      <View style={favoritesScreenStyles.favoriteCardPlaceholder}>
                        <Text style={favoritesScreenStyles.errorLoadingText}>Failed to load weather</Text>
                        <Text style={favoritesScreenStyles.errorLoadingSubText}>Tap to retry</Text>
                      </View>
                    )}
                  </TouchableOpacity>
                </View>
              );
            })}
          </View>
        ) : (
          <View style={favoritesScreenStyles.noFavoritesContainer}>
            <Icon name="heart-outline" size={60} color="#A0A0A0" />
            <Text style={favoritesScreenStyles.noFavoritesText}>No favorite locations yet.</Text>
            <Text style={favoritesScreenStyles.noFavoritesSubText}>
              Search for a location in the 'Current' tab and tap the heart icon to add it to your favorites.
            </Text>
          </View>
        )}
      </ScrollView>
    </View>
  );
};

const favoritesScreenStyles = StyleSheet.create({
  fullScreenContainer: {
    flex: 1,
    backgroundColor: '#213366',
  },
  scrollView: {
    flex: 1,
  },
  scrollViewContent: {
    flexGrow: 1,
    paddingBottom: 20,
  },
  favoritesHeader: {
    color: '#FFFFFF',
    fontSize: 20,
    fontWeight: 'bold',
    marginHorizontal: 20,
    marginBottom: 15,
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 10,
  },
  noFavoritesContainer: {
    alignItems: 'center',
    marginTop: 50,
    paddingHorizontal: 30,
  },
  noFavoritesText: {
    color: '#A0A0A0',
    fontSize: 20,
    fontWeight: 'bold',
    marginTop: 10,
  },
  noFavoritesSubText: {
    color: '#808080',
    fontSize: 14,
    textAlign: 'center',
    marginTop: 5,
  },
  favoritesList: {
    marginHorizontal: 20,
  },
  favoriteItemContainer: {
    marginBottom: 10,
    backgroundColor: 'rgba(255,255,255,0.08)',
    borderRadius: 10,
    overflow: 'hidden',
  },
  favoriteItemHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 15,
    paddingTop: 10,
    paddingBottom: 5,
  },
  favoriteLocationName: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: 'bold',
  },
  removeButton: {
    padding: 5,
  },
  loadingContainer: {
    alignItems: 'center',
    marginTop: 50,
  },
  loadingText: {
    color: '#A0A0A0',
    marginTop: 10,
  },
  favoriteCardPlaceholder: {
    padding: 15,
    alignItems: 'center',
  },
  errorLoadingText: {
    color: '#FF5C5C',
    fontSize: 16,
  },
  errorLoadingSubText: {
    color: '#FF5C5C',
    fontSize: 12,
    marginTop: 5,
  },
});

export default FavoritesScreen;
