package vazkii.thingy;

import java.util.Random;

public class NoiseGenerator {

	public float[][] valueTable;

	int size;
	int samples;
	Random rand;

	public NoiseGenerator(int size, int samples, long seed) {
		this.size = size;
		this.samples = samples;

		valueTable = new float[size][size];
		rand = new Random(seed);

		build();
	}	

	void build() {
		for(int i = 0; i < samples; i++)
			buildSample(i);
	}

	void buildSample(int sample) {
		int tableSize = (int) Math.pow(2, sample + 2);
		float ampl = 1F / (sample + 1);

		int tempTableSize = (int) tableSize + 2;
		int gridSize = size / tableSize;
		float[][] tempTable = new float[tempTableSize][tempTableSize];

		for(int i = 0; i < tempTableSize; i++)
			for(int j = 0; j < tempTableSize; j++) {
				float val = rand.nextFloat();
				tempTable[i][j] = val;
			}

		for(int i = 0; i < tableSize; i++)
			for(int j = 0; j < tableSize; j++) {
				int bx = i * gridSize;
				int by = j * gridSize;

				for(int i1 = 0; i1 < gridSize; i1++)
					for(int j1 = 0; j1 < gridSize; j1++)
						valueTable[bx + i1][by + j1] += interpNoiseAt(tempTable, i + 1, j + 1, i1, j1, gridSize) * ampl;
			}
	}

	float interpNoiseAt(float[][] table, int x, int y, int x1, int y1, int gs) {
		float fx = (float) x1 / (float) gs;
		float fy = (float) y1 / (float) gs;

		float xInterp1 = lerp(table[x][y], table[x + 1][y], fx);
		float xInterp2 = lerp(table[x][y + 1], table[x + 1][y + 1], fx);
		float yInterp = lerp(xInterp1, xInterp2, fy);

		return yInterp;
	}

	float lerp(float a, float b, float x) {
		return a + x * (b - a);
	}

}