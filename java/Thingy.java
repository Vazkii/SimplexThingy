package vazkii.thingy;

import java.awt.Color;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;

import javax.imageio.ImageIO;

public class Thingy {

	static final int SIZE = 2048;
	static final int OCTAVES = 6;
	
	static int[] colors = new int[255];
	
	static {
		fill(0, 90, 0x001dae);
		fill(90, 110, 0x062ae1);
		fill(110, 135, 0x3254ff);
		fill(135, 142, 0xecd300);
		fill(142, 164, 0x12bd01);
		fill(164, 176, 0x0a6e00);
		fill(176, 192, 0x663c00);
		fill(192, 255, 0xeeeeee);
	}
	
	public static void main(String[] args) {
		int octaves = Math.min(OCTAVES, log2i(SIZE));
		long time = System.currentTimeMillis();
		
		log("Starting");
		long seed = System.nanoTime();
		
		log("Seed is " + seed);
		log("Size is " + SIZE);
		log("Octave Count is " + octaves);
		
		NoiseGenerator gen = new NoiseGenerator(SIZE, octaves, seed);
		
		BufferedImage imageGray = new BufferedImage(SIZE, SIZE, BufferedImage.TYPE_INT_RGB);
		BufferedImage imageMap = new BufferedImage(SIZE, SIZE, BufferedImage.TYPE_INT_RGB);
		BufferedImage imageHue = new BufferedImage(SIZE, SIZE, BufferedImage.TYPE_INT_RGB);

		float total = 1F;
		for(int i = 1; i < OCTAVES - 1; i++)
			total += 1F / (i + 1);
		log("Total possible value is " + total);
		
		log("Building Buffered Images");
		for(int i = 0; i < SIZE; i++) {
			for(int j = 0; j < SIZE; j++) {
				float val = gen.valueTable[i][j] / total;
				int gray = (int) (val * 255);
				int colorGray = gray << 16 | gray << 8 | gray;
				int colorHue = Color.HSBtoRGB(val, 1F, 1F);
				
				Color cColor = new Color(colors[gray] + (192 << 24));
				Color cGray = new Color(gray, gray, gray);
				Color c = blend(cColor, cGray);
				int colorMap = c.getRGB();
				
				imageGray.setRGB(i, j, colorGray);
				imageHue.setRGB(i, j, colorHue);
				imageMap.setRGB(i, j, colorMap);
			}
		}
	
		log("Outputting Images");
		File fGray = new File(".", "outputGray.png");
		File fHue = new File(".", "outputHue.png");
		File fMap = new File(".", "outputMap.png");

		try {
			ImageIO.write(imageGray, "png", fGray);
			ImageIO.write(imageHue, "png", fHue);
			ImageIO.write(imageMap, "png", fMap);
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		log("Images written to: ");
		log(" - " + fGray.getAbsolutePath());
		log(" - " + fHue.getAbsolutePath());
		log(" - " + fMap.getAbsolutePath());
		
		long timeDiff = System.currentTimeMillis() - time;
		System.out.println("Done! Took " + timeDiff + "ms.");
	}
	
	private static void fill(int from, int to, int color) {
		for(int i = from; i < to; i++)
			colors[i] = color;
	}
	
	public static void log(String msg) {
		System.out.println(msg);
	}
	
	public static int log2i(int i) {
		return (int) (Math.log10(i) / Math.log10(2));
	}
	
	public static Color blend(Color c0, Color c1) {
	    double totalAlpha = c0.getAlpha() + c1.getAlpha();
	    double weight0 = c0.getAlpha() / totalAlpha;
	    double weight1 = c1.getAlpha() / totalAlpha;

	    double r = weight0 * c0.getRed() + weight1 * c1.getRed();
	    double g = weight0 * c0.getGreen() + weight1 * c1.getGreen();
	    double b = weight0 * c0.getBlue() + weight1 * c1.getBlue();
	    double a = Math.max(c0.getAlpha(), c1.getAlpha());

	    return new Color((int) r, (int) g, (int) b, (int) a);
	  }

}
