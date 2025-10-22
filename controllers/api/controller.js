import fetch from 'node-fetch';
import { API_URL } from '../config/apiConfig.js';

export const getExternalData = async (req, res) => {
  try {
    const response = await fetch(API_URL);
    const data = await response.json();
    res.status(200).json(data);
  } catch (error) {
    console.error('Error fetching API data:', error);
    res.status(500).json({ message: 'Failed to fetch data from external API' });
  }
};

