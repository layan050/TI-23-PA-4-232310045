import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

const CustomHeader = () => {
  return (
    <View style={headerStyles.container}>
      <Text style={headerStyles.title}>WeatherApp</Text>
    </View>
  );
};

const headerStyles = StyleSheet.create({
  container: {
    backgroundColor: '#1E2B54',
    paddingTop: 50,
    paddingBottom: 15,
    alignItems: 'center',
    justifyContent: 'center',
    borderBottomWidth: 0,
    elevation: 0,
    shadowOpacity: 0,
  },
  title: {
    color: '#FFFFFF',
    fontSize: 22,
    fontWeight: 'bold',
  },
});

export default CustomHeader;
