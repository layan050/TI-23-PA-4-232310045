import React, { useState, useEffect, useCallback } from 'react';
import { View, TextInput, TouchableOpacity, Text, StyleSheet, FlatList, ActivityIndicator, Alert } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { getCitySuggestions, getCoordinatesForCity } from '../api/weatherApi';

const SearchBar = ({ onSearchSubmit, placeholder = "Search city...", showAddButton = false }) => {
  const [searchText, setSearchText] = useState('');
  const [suggestions, setSuggestions] = useState([]);
  const [showSuggestions, setShowSuggestions] = useState(false);
  const [isSearching, setIsSearching] = useState(false);

  useEffect(() => {
    const delayDebounceFn = setTimeout(async () => {
      if (searchText.length > 2) {
        setIsSearching(true);
        try {
          const fetchedSuggestions = await getCitySuggestions(searchText);
          setSuggestions(fetchedSuggestions);
          setShowSuggestions(true);
        } catch (error) {
          console.error('Error fetching suggestions:', error);
          setSuggestions([]);
        } finally {
          setIsSearching(false);
        }
      } else {
        setSuggestions([]);
        setShowSuggestions(false);
        setIsSearching(false);
      }
    }, 500);

    return () => clearTimeout(delayDebounceFn);
  }, [searchText]);

  const handleSelectSuggestion = useCallback(async (suggestion) => {
    setSearchText(suggestion.name);
    setShowSuggestions(false);
    setIsSearching(false);

    if (onSearchSubmit) {
      onSearchSubmit(suggestion.name, suggestion.lat, suggestion.lon);
    }
  }, [onSearchSubmit]);

  const handleSubmit = useCallback(async () => {
    if (searchText.trim().length > 0) {
      setIsSearching(true);
      setShowSuggestions(false);
      try {
        const coords = await getCoordinatesForCity(searchText);
        if (coords) {
          if (onSearchSubmit) {
            onSearchSubmit(coords.name, coords.lat, coords.lon);
          }
        } else {
          Alert.alert('City Not Found', 'Please check your city name again.');
        }
      } catch (error) {
        console.error('Error on search submit:', error);
        Alert.alert('Error', 'Failed to search for city.');
      } finally {
        setIsSearching(false);
      }
    }
  }, [searchText, onSearchSubmit]);


  return (
    <View style={styles.container}>
      <View style={styles.searchRow}>
        <TextInput
          style={styles.input}
          placeholder={placeholder}
          placeholderTextColor="#A0A0A0"
          value={searchText}
          onChangeText={setSearchText}
          onSubmitEditing={handleSubmit}
          returnKeyType="search"
          keyboardAppearance="dark"
        />
        {(isSearching && showSuggestions) ? (
          <ActivityIndicator size="small" color="#4285F4" style={styles.spinner} />
        ) : (
          <TouchableOpacity onPress={handleSubmit} style={styles.searchButton}>
            <Ionicons name="search" size={24} color="#FFF" />
          </TouchableOpacity>
        )}
      </View>

      {showSuggestions && suggestions.length > 0 && (
        <View style={styles.suggestionsContainer}>
          <FlatList
            data={suggestions}
            keyExtractor={(item, index) => `${item.name}-${index}`}
            renderItem={({ item }) => (
              <TouchableOpacity onPress={() => handleSelectSuggestion(item)} style={styles.suggestionItem}>
                <Text style={styles.suggestionText}>{item.name}</Text>
              </TouchableOpacity>
            )}
            style={styles.suggestionsList}
            keyboardShouldPersistTaps="always"
          />
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: 15,
    paddingVertical: 10,
    backgroundColor: '#1E2B54',
    zIndex: 10,
  },
  searchRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  input: {
    flex: 1,
    height: 45,
    borderColor: 'rgba(255,255,255,0.2)',
    borderWidth: 1,
    borderRadius: 25,
    paddingHorizontal: 15,
    marginRight: 10,
    backgroundColor: '#2D407A',
    fontSize: 16,
    color: '#FFFFFF',
  },
  searchButton: {
    backgroundColor: '#4285F4',
    borderRadius: 25,
    width: 45,
    height: 45,
    justifyContent: 'center',
    alignItems: 'center',
  },
  spinner: {
    width: 45,
    height: 45,
    marginRight: 10,
    justifyContent: 'center',
    alignItems: 'center',
  },
  suggestionsContainer: {
    position: 'absolute',
    top: 65,
    left: 15,
    right: 15,
    backgroundColor: '#2D407A',
    borderColor: 'rgba(255,255,255,0.2)',
    borderWidth: 1,
    borderRadius: 8,
    maxHeight: 200,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
  },
  suggestionsList: {
    paddingVertical: 5,
  },
  suggestionItem: {
    paddingVertical: 10,
    paddingHorizontal: 15,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,255,255,0.1)',
  },
  suggestionText: {
    color: '#FFFFFF',
    fontSize: 16,
  },
});

export default SearchBar;
