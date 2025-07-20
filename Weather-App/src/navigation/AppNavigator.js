import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, ActivityIndicator, StyleSheet, Alert } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createMaterialTopTabNavigator } from '@react-navigation/material-top-tabs';
import * as Location from 'expo-location';

import CurrentWeatherScreen from '../screens/CurrentWeatherScreen';
import ForecastScreen from '../screens/ForecastScreen';
import FavoritesScreen from '../screens/FavoritesScreen';
import CustomHeader from '../components/CustomHeader';
import SearchBar from '../components/SearchBar';
import { getWeatherData } from '../api/weatherApi';
import { saveFavoriteLocations, getFavoriteLocations } from '../api/storage';

const Tab = createMaterialTopTabNavigator();

const REFRESH_INTERVAL_MS = 10 * 60 * 1000;

const AppNavigator = () => {
  const [weatherData, setWeatherData] = useState(null);
  const [currentLocationName, setCurrentLocationName] = useState('Loading location...');
  const [currentCoordinates, setCurrentCoordinates] = useState(null);
  const [favoriteLocations, setFavoriteLocations] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);
  const [isCurrentLocationFavorite, setIsCurrentLocationFavorite] = useState(false);
  const [locationErrorMsg, setLocationErrorMsg] = useState(null);

  const fetchWeatherForLocation = useCallback(async (lat, lon, name = null) => {
    setIsLoading(true);
    setError(null);
    try {
      const data = await getWeatherData(lat, lon);
      if (data) {
        setWeatherData(data);
        setCurrentCoordinates({ lat, lon });
        if (name) {
          setCurrentLocationName(name);
        } else {
          const reverseGeo = await Location.reverseGeocodeAsync({ latitude: lat, longitude: lon });
          if (reverseGeo && reverseGeo.length > 0) {
            setCurrentLocationName(reverseGeo[0].city || reverseGeo[0].name || 'Unknown location');
          } else {
            setCurrentLocationName('Unknown location');
          }
        }
      } else {
        setError('Failed to load weather data. Data is invalid or empty.');
      }
    } catch (err) {
      console.error('Error in fetchWeatherForLocation:', err);
      setError('Failed to load weather data. Check your internet connection or try again later.');
      setWeatherData(null);
    } finally {
      setIsLoading(false);
    }
  }, []);

  const fetchInitialData = useCallback(async () => {
    setIsLoading(true);
    setError(null);
    try {
      let { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') {
        setLocationErrorMsg('Location access denied. Weather data may be inaccurate.');
        setError('Location permission denied.');
        setIsLoading(false);
        return;
      }
      setLocationErrorMsg(null);
      let location = await Location.getCurrentPositionAsync({});
      await fetchWeatherForLocation(location.coords.latitude, location.coords.longitude);
    } catch (err) {
      console.error('Error fetching initial location or weather:', err);
      setError('Failed to get initial location or weather data. Ensure GPS is active.');
    } finally {
      setIsLoading(false);
    }
  }, [fetchWeatherForLocation]);

  useEffect(() => {
    fetchInitialData();
    const interval = setInterval(fetchInitialData, REFRESH_INTERVAL_MS);
    return () => clearInterval(interval);
  }, [fetchInitialData]);

  useEffect(() => {
    const loadFavorites = async () => {
      const savedFavorites = await getFavoriteLocations();
      setFavoriteLocations(savedFavorites);
    };
    loadFavorites();
  }, []);

  useEffect(() => {
    if (currentCoordinates && favoriteLocations.length > 0) {
      const isFavorite = favoriteLocations.some(
        (loc) => loc.lat === currentCoordinates.lat && loc.lon === currentCoordinates.lon
      );
      setIsCurrentLocationFavorite(isFavorite);
    } else {
      setIsCurrentLocationFavorite(false);
    }
  }, [currentCoordinates, favoriteLocations]);

  const handleSearchSubmit = useCallback(async (cityName, lat, lon) => {
    if (cityName && lat && lon) {
      await fetchWeatherForLocation(lat, lon, cityName);
    } else {
      setError('Invalid or unfound city name.');
    }
  }, [fetchWeatherForLocation]);

  const handleToggleFavorite = useCallback(async () => {
    if (!currentCoordinates || !currentLocationName) {
      Alert.alert('Error', 'No location selected to add to favorites.');
      return;
    }

    const newFavorite = {
      name: currentLocationName,
      lat: currentCoordinates.lat,
      lon: currentCoordinates.lon,
    };

    let updatedFavorites;
    if (isCurrentLocationFavorite) {
      updatedFavorites = favoriteLocations.filter(
        (loc) => !(loc.lat === newFavorite.lat && loc.lon === newFavorite.lon)
      );
    } else {
      updatedFavorites = [...favoriteLocations, newFavorite];
    }
    setFavoriteLocations(updatedFavorites);
    await saveFavoriteLocations(updatedFavorites);
    setIsCurrentLocationFavorite(!isCurrentLocationFavorite);
  }, [currentCoordinates, currentLocationName, favoriteLocations, isCurrentLocationFavorite]);

  const handleRemoveFavorite = useCallback(async (locationToRemove) => {
    const updatedFavorites = favoriteLocations.filter(
      (loc) => !(loc.lat === locationToRemove.lat && loc.lon === locationToRemove.lon)
    );
    setFavoriteLocations(updatedFavorites);
    await saveFavoriteLocations(updatedFavorites);
    if (currentCoordinates && locationToRemove.lat === currentCoordinates.lat && locationToRemove.lon === currentCoordinates.lon) {
      setIsCurrentLocationFavorite(false);
    }
  }, [favoriteLocations, currentCoordinates]);

  const handleSelectFavorite = useCallback(async (location) => {
    await fetchWeatherForLocation(location.lat, location.lon, location.name);
  }, [fetchWeatherForLocation]);

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#4285F4" />
        <Text style={styles.loadingText}>Loading weather data...</Text>
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>An error occurred:</Text>
        <Text style={styles.errorTextDetail}>{error}</Text>
        {locationErrorMsg && <Text style={styles.errorTextDetail}>{locationErrorMsg}</Text>}
        <Text style={styles.errorTextDetail}>Please try again.</Text>
      </View>
    );
  }

  return (
    <NavigationContainer>
      <CustomHeader />
      <SearchBar onSearchSubmit={handleSearchSubmit} />

      <Tab.Navigator
        initialRouteName="Current"
        screenOptions={{
          tabBarActiveTintColor: '#FFFFFF',
          tabBarInactiveTintColor: '#A0A0A0',
          tabBarIndicatorStyle: { backgroundColor: '#4285F4', height: 4 },
          tabBarStyle: { backgroundColor: '#213366' },
          tabBarLabelStyle: { fontSize: 14, fontWeight: 'bold' },
        }}
      >
        <Tab.Screen name="Current">
          {props => (
            <CurrentWeatherScreen
              {...props}
              currentWeatherData={weatherData?.current}
              currentLocationName={currentLocationName}
              onSearchSubmit={handleSearchSubmit}
              isCurrentLocationFavorite={isCurrentLocationFavorite}
              onToggleFavorite={handleToggleFavorite}
              onRefresh={fetchInitialData}
            />
          )}
        </Tab.Screen>
        <Tab.Screen name="Forecast">
          {props => (
            <ForecastScreen
              {...props}
              dailyForecastData={weatherData?.daily}
              currentLocationName={currentLocationName}
              onSearchSubmit={handleSearchSubmit}
            />
          )}
        </Tab.Screen>
        <Tab.Screen name="Favorites">
          {props => (
            <FavoritesScreen
              {...props}
              favoriteLocations={favoriteLocations}
              onSelectFavorite={handleSelectFavorite}
              onRemoveFavorite={handleRemoveFavorite}
              onSearchSubmit={handleSearchSubmit}
            />
          )}
        </Tab.Screen>
      </Tab.Navigator>
    </NavigationContainer>
  );
};

const styles = StyleSheet.create({
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#213366',
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: '#FFFFFF',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#213366',
    padding: 20,
  },
  errorText: {
    color: '#FF5C5C',
    fontSize: 18,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 5,
  },
  errorTextDetail: {
    color: '#FF5C5C',
    fontSize: 14,
    textAlign: 'center',
    marginBottom: 5,
  },
});

export default AppNavigator;
